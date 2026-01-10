<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Revision;
use App\Models\Wiki;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    //Заглавная конкретной вики: список всех статей
    //(Аналог Служебная:Все страницы)
    public function index(string $wikiName, Request $request) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $user = auth()->user();
            if ($wiki) {
                if ($user != null) {
                    $can_check_revisions = $user->can('check_revisions', $wiki->url);
                } else {
                    $can_check_revisions = false;
                }
            }
            if ($can_check_revisions) {
                $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            } else {
                if ($user != null) {
                    // Авторизованный пользователь видит статьи с одобренными правками, своими правками или отпатрулированными
                    $articleIds = DB::table('revisions')
                        ->select('article_id')
                        ->whereNull('deleted_at')
                        ->where(function($query) use ($user) {
                            $query->where('is_approved', true)
                                ->orWhere('user_id', $user->id)
                                ->orWhere('is_patrolled', true);
                        })
                        ->distinct()
                        ->pluck('article_id');
                    
                    $articles = Article::where('wiki_id', $wiki->id)
                        ->whereNull('deleted_at')
                        ->whereIn('id', $articleIds)
                        ->get();
                } else {
                    // Неавторизованный пользователь видит статьи с отпатрулированными и одобренными правками, или своими анонимными правками (по IP) в течение 15 минут
                    $userIp = $request->ip();
                    $articleIds = DB::table('revisions')
                        ->select('article_id')
                        ->whereNull('deleted_at')
                        ->where(function($query) use ($userIp) {
                            $query->where(function($q) {
                                $q->where('is_approved', true)
                                  ->where('is_patrolled', true);
                            })
                            ->orWhere(function($q) use ($userIp) {
                                $q->where('user_id', 0)
                                  ->where('user_ip', $userIp)
                                  ->where('created_at', '>=', now()->subMinutes(15));
                            });
                        })
                        ->distinct()
                        ->pluck('article_id');
                    
                    $articles = Article::where('wiki_id', $wiki->id)
                        ->whereNull('deleted_at')
                        ->whereIn('id', $articleIds)
                        ->get();
                }
            }

            return view('show-all-articles', compact('articles', 'wiki'));
        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //Показывает вики-страницу
    public function show(string $wikiName, string $articleName, Request $request) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        $user = auth()->user();
        if ($wiki) {
            if ($user != null) {
                $can_check_revisions = $user->can('check_revisions', $wiki->url);
            } else {
                $can_check_revisions = false;
            }

            //dd($can_check_revisions);

            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();

            if($articles) {
                $article = $articles->where('url_title', $articleName)->first();

                if($article) {

                    if ($can_check_revisions) {
                        $revision = Revision::where('article_id', $article->id)
                        //->where('deleted_at', '')
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'desc')->first();
                    } else {
                        if ($user != null) {
                            // Авторизованный пользователь видит: свои правки (даже неодобренные), отпатрулированные и одобренные правки
                            $revision = Revision::where('article_id', $article->id)
                            ->whereNull('deleted_at')
                            ->where(function($query) use ($user) {
                                $query->where('is_approved', true)
                                    ->orWhere(function($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    })
                                    ->orWhere('is_patrolled', true);
                            })
                            ->orderBy('id', 'desc')->first();
                        } else {
                            // Неавторизованный пользователь видит: отпатрулированные и одобренные правки, или свои анонимные правки (по IP) в течение 15 минут
                            $userIp = $request->ip();
                            $revision = Revision::where('article_id', $article->id)
                            ->whereNull('deleted_at')
                            ->where(function($query) use ($userIp) {
                                $query->where(function($q) {
                                    $q->where('is_approved', true)
                                      ->where('is_patrolled', true);
                                })
                                ->orWhere(function($q) use ($userIp) {
                                    $q->where('user_id', 0)
                                      ->where('user_ip', $userIp)
                                      ->where('created_at', '>=', now()->subMinutes(15));
                                });
                            })
                            ->orderBy('id', 'desc')->first();
                        }
                    }
                    
                    //$user = auth()->user();

                    if ($user != null) {
                        $userId = $user->id;
                        $userName = $user->name;
                        $userCanDeleteComments = $user->can('delete_comments', $wiki->url);
                    } else {
                        $userId = 0;
                        $userName = 'Анонимный участник';
                        $userCanDeleteComments = false;
                    }

                    if ($revision) {
                        return view('article', compact('revision', 'wiki', 'article',
                        'userId', 'userName', 'userCanDeleteComments'));
                    } else {
                        return response(__('Article does not exist'), 404)
                            ->header('Content-Type', 'text/plain');
                    }
                }
                 else {
                    return response(__('Article does not exist'), 404)
                        ->header('Content-Type', 'text/plain');
                 }
            } else {
                return response(__('No articles'), 404)
                    ->header('Content-Type', 'text/plain');
            }
        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //Форма создания статьи
    public function create(string $wikiName) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            return view('create-article', compact('wiki'));
        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //POST-ручка для создания статьи
    public function store(string $wikiName, Request $request) {
        //dd('test0');
        $data = request()->validate([
            'title' => 'string',
            'url_title' => 'string',
            'content' => 'string',
        ]);

        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            //dd(auth()->user());
            $my_article = [
                'wiki_id' => $wiki->id,
                'url_title' => $data['url_title'],
                'title' => $data['title'],
            ];
            $created_article = Article::create($my_article);

            if (auth()->user() != null) {
                $user_id = auth()->user()->id;
            } else {
                $user_id = 0;
            }
            $user_ip = $request->ip();
            $my_revision = [
                'article_id' => $created_article->id,
                'title' =>  $data['title'],
                'url_title' => $data['url_title'],
                'content' => $data['content'],
                'user_id' => $user_id,
                'user_ip' => $user_ip,
            ];

            Revision::create($my_revision);

            return redirect()->route('articles.show', [$wiki->url, $created_article->url_title]);

        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //Форма правки статьи
    public function edit(string $wikiName, string $articleName, Request $request) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            if ($articles) {
                $article = $articles->where('url_title', $articleName)->first();
                if ($article) {

                    $user = auth()->user();

                    if ($user != null) {
                        $can_check_revisions = $user->can('check_revisions', $wiki->url);
                    } else {
                        $can_check_revisions = false;
                    }

                    if ($can_check_revisions) {
                        $revision = Revision::where('article_id', $article->id)
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'desc')
                        ->first();
                    } else {
                        if ($user != null) {
                            // Авторизованный пользователь видит: свои правки (даже неодобренные), отпатрулированные и одобренные правки
                            $revision = Revision::where('article_id', $article->id)
                            ->whereNull('deleted_at')
                            ->where(function($query) use ($user) {
                                $query->where('is_approved', true)
                                    ->orWhere(function($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    })
                                    ->orWhere('is_patrolled', true);
                            })
                            ->orderBy('id', 'desc')
                            ->first();
                        } else {
                            // Неавторизованный пользователь видит: отпатрулированные и одобренные правки, или свои анонимные правки (по IP) в течение 15 минут
                            $userIp = $request->ip();
                            $revision = Revision::where('article_id', $article->id)
                            ->whereNull('deleted_at')
                            ->where(function($query) use ($userIp) {
                                $query->where(function($q) {
                                    $q->where('is_approved', true)
                                      ->where('is_patrolled', true);
                                })
                                ->orWhere(function($q) use ($userIp) {
                                    $q->where('user_id', 0)
                                      ->where('user_ip', $userIp)
                                      ->where('created_at', '>=', now()->subMinutes(15));
                                });
                            })
                            ->orderBy('id', 'desc')
                            ->first();
                        }
                    }

                    if ($revision) {
                        return view('edit', compact('article', 'revision', 'wiki'));
                    } else {
                        return response(__('Article does not exist'), 404)
                        ->header('Content-Type', 'text/plain');
                    }
                } else {
                    return response(__('Article does not exist'), 404)
                        ->header('Content-Type', 'text/plain');
                }

            } else {
                return response(__('No articles'), 404)
                    ->header('Content-Type', 'text/plain');
            }
        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //POST-ручка для формы правки статьи
    public function update($wikiName, $articleName, Request $request)
    {
        $data = request()->validate([
            'title' => 'string',
            'url_title' => 'string',
            'content' => 'string',
        ]);

        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $my_article = [
                'wiki_id' => $wiki->id,
                'url_title' => $data['url_title'],
                'title' => $data['title'],
            ];
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            if ($articles) {

                $my_article2 = $articles->where('url_title', $articleName)->first();

                if ($my_article2) {

                    $my_article2->update($my_article);

                    if (auth()->user() != null) {
                        $user_id = auth()->user()->id;
                    } else {
                        $user_id = 0;
                    }

                    $user_ip = $request->ip();

                    $my_revision = [
                        'article_id' => $my_article2->id,
                        'title' =>  $data['title'],
                        'url_title' => $data['url_title'],
                        'content' => $data['content'],
                        'user_id' => $user_id,
                        'user_ip' => $user_ip,
                    ];

                    Revision::create($my_revision);

                    return redirect()->route('articles.show', [$wiki->url, $my_article2->url_title]);
                }   else {
                        return response(__('Error'), 500)
                            ->header('Content-Type', 'text/plain');
                }
            } else {
                return response(__('Error'), 500)
                    ->header('Content-Type', 'text/plain');
            }

        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //DELETE-ручка для удаления статьи
    //(требуются технические права)
    public function destroy(string $wikiName, string $articleName): Response
    {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            if ($articles) {

                $my_article2 = $articles->where('url_title', $articleName)->first();

                if ($my_article2) {

                    $my_article2->delete();
                    return response(__('Article was deleted'), 200)
                        ->header('Content-Type', 'text/plain');
                }   else {
                        return response(__('Error'), 500)
                            ->header('Content-Type', 'text/plain');
                }
            } else {
                return response(__('Error'), 500)
                    ->header('Content-Type', 'text/plain');
            }

        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //Список удалённых статей
    //(требуются технические права)
    public function trash(string $wikiName, Request $request) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $user = auth()->user();
            if ($user != null) {
                $can_check_revisions = $user->can('check_revisions', $wiki->url);
            } else {
                $can_check_revisions = false;
            }

            if ($can_check_revisions) {
                $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();
            } else {
                if ($user != null) {
                    // Авторизованный пользователь видит удаленные статьи с одобренными правками, своими правками или отпатрулированными
                    $articleIds = DB::table('revisions')
                        ->select('article_id')
                        ->whereNull('deleted_at')
                        ->where(function($query) use ($user) {
                            $query->where('is_approved', true)
                                ->orWhere('user_id', $user->id)
                                ->orWhere('is_patrolled', true);
                        })
                        ->distinct()
                        ->pluck('article_id');
                    
                    $articles = Article::onlyTrashed()
                        ->where('wiki_id', $wiki->id)
                        ->whereIn('id', $articleIds)
                        ->get();
                } else {
                    // Неавторизованный пользователь видит удаленные статьи с отпатрулированными и одобренными правками, или своими анонимными правками (по IP) в течение 15 минут
                    $userIp = $request->ip();
                    $articleIds = DB::table('revisions')
                        ->select('article_id')
                        ->whereNull('deleted_at')
                        ->where(function($query) use ($userIp) {
                            $query->where(function($q) {
                                $q->where('is_approved', true)
                                  ->where('is_patrolled', true);
                            })
                            ->orWhere(function($q) use ($userIp) {
                                $q->where('user_id', 0)
                                  ->where('user_ip', $userIp)
                                  ->where('created_at', '>=', now()->subMinutes(15));
                            });
                        })
                        ->distinct()
                        ->pluck('article_id');
                    
                    $articles = Article::onlyTrashed()
                        ->where('wiki_id', $wiki->id)
                        ->whereIn('id', $articleIds)
                        ->get();
                }
            }

            return view('trash', compact('articles', 'wiki'));
        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //Просмотр удалённой статьи
    //(требуются технические права)
    public function show_deleted(string $wikiName, string $articleName, Request $request) {
        $wiki = Wiki::where('url', $wikiName)->first();
        if ($wiki) {
            $user = auth()->user();
            if ($user != null) {
                $can_check_revisions = $user->can('check_revisions', $wiki->url);
            } else {
                $can_check_revisions = false;
            }
            $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();

            if($articles) {
                $article = $articles->where('url_title', $articleName)->first();

                if($article) {

                    if ($can_check_revisions) {
                        $revision = Revision::where('article_id', $article->id)
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'desc')
                        ->first();
                    } else {
                        if ($user != null) {
                            // Авторизованный пользователь видит: свои правки (даже неодобренные), отпатрулированные и одобренные правки
                            $revision = Revision::where('article_id', $article->id)
                            ->whereNull('deleted_at')
                            ->where(function($query) use ($user) {
                                $query->where('is_approved', true)
                                    ->orWhere(function($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    })
                                    ->orWhere('is_patrolled', true);
                            })
                            ->orderBy('id', 'desc')
                            ->first();
                        } else {
                            // Неавторизованный пользователь видит: отпатрулированные и одобренные правки, или свои анонимные правки (по IP) в течение 15 минут
                            $userIp = $request->ip();
                            $revision = Revision::where('article_id', $article->id)
                            ->whereNull('deleted_at')
                            ->where(function($query) use ($userIp) {
                                $query->where(function($q) {
                                    $q->where('is_approved', true)
                                      ->where('is_patrolled', true);
                                })
                                ->orWhere(function($q) use ($userIp) {
                                    $q->where('user_id', 0)
                                      ->where('user_ip', $userIp)
                                      ->where('created_at', '>=', now()->subMinutes(15));
                                });
                            })
                            ->orderBy('id', 'desc')
                            ->first();
                        }
                    }

                    if ($revision) {
                        return view('deleted-article', compact('revision', 'wiki', 'article'));
                    }

                    else {
                        return response(__('Article does not exist'), 404)
                        ->header('Content-Type', 'text/plain');
                    }
                }
                 else {
                    return response(__('Article does not exist'), 404)
                        ->header('Content-Type', 'text/plain');
                 }
            } else {
                return response(__('No articles'), 404)
                    ->header('Content-Type', 'text/plain');
            }
        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

    //POST-ручка для восстановления стаьи
    //(требуются технические права)
    public function restore(string $wikiName, string $articleName): Response {
        $wiki = Wiki::where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();
            if ($articles) {

                $my_article2 = $articles->where('url_title', $articleName)->first();
                if ($my_article2) {

                    $my_article2->restore();
                    return response(__('Article was restored'), 200)
                        ->header('Content-Type', 'text/plain');
                }   else {
                        return response(__('Error'), 500)
                            ->header('Content-Type', 'text/plain');
                }
            } else {
                return response(__('Error'), 500)
                    ->header('Content-Type', 'text/plain');
            }

        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }

}
