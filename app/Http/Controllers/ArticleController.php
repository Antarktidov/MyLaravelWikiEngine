<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wiki;
use App\Models\Article;
use App\Models\Revision;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleController extends Controller
{
    //use SoftDeletes;

    public function index($wikiName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();

            return view('show-all-articles', compact('articles', 'wiki'));
        } else {
            return 'Указанной вики не сущесвует';
        }
    }

    public function show_article($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();

            if($articles) {
                $article = $articles->where('url_title', $articleName)->first();

                if($article) {
                    $revision = DB::table('revisions')
                    ->where('article_id', $article->id)
                    //->where('deleted_at', '')
                    ->orderBy('id', 'desc')->first();
                    if ($revision) {
                        return view('article', compact('revision', 'wiki', 'article'));
                    } else {
                        return 'Похоже, все правки данной статьи скрыты';
                    }
                }
                 else {
                    return 'Такой статьи нет';
                 }
            } else {
                return 'Нет статей';
            }
        } else {
            return 'Указанной вики не сущесвует';
        }
    }

    public function history($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
                $articles = Article::where('wiki_id', $wiki->id)->get();
                if($articles) {
                    $article = $articles->where('url_title', $articleName)->first();
                    if($article) {
                        $revisions = Revision::all();
                        $users = User::all();
                            return view('history', compact('article', 'revisions',
                            'users', 'wiki'));
                            } else {
                                return 'Такой статьи нет';
                            }
            } else {
                return 'Нет статей';
            }
        } else {
            return 'Указанной вики не сущесвует';
        }
    }

    public function show_deleted_article_history($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
                $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();
                if($articles) {
                    $article = $articles->where('url_title', $articleName)->first();
                    if($article) {
                        $revisions = Revision::all();
                        $users = User::all();
                            return view('show_deleted_article_history', compact('article', 'revisions',
                            'users', 'wiki'));
                            } else {
                                return 'Такой статьи нет';
                            }
            } else {
                return 'Нет статей';
            }
        } else {
            return 'Указанной вики не сущесвует';
        }
    }

    public function deleted_history($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $article = Article::where('wiki_id', $wiki->id)->where('url_title', $articleName)->first();
            if ($article) {
                $revisions = Revision::onlyTrashed()->where('article_id', $article->id)->get();
                $users = User::all();
                return view('deleted_history', compact('article', 'revisions', 'users', 'wiki'));
            } else {
                return 'Такой статьи нет';
            }
        } else {
            return 'Указанной вики не существует';
        }
    }
    

    public function create($wikiName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            return view('create-article', compact('wiki'));
        } else {
            return 'Указанной вики не сущесвует';
        }
    }

    public function store($wikiName, Request $request) {
        //dd('test0');
        $data = request()->validate([
            'title' => 'string',
            'url_title' => 'string',
            'content' => 'string',
        ]);

        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
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
            return 'Указанной вики не сущесвует';
        }
    }

    public function edit($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();
            if ($articles) {
                $article = $articles->where('url_title', $articleName)->first();
                if ($article) {
                    $revision = DB::table('revisions')->where('article_id', $article->id)->orderBy('id', 'desc')->first();
                    return view('edit', compact('article', 'revision', 'wiki'));
                } else {
                    return 'Такой статьи нет';
                }

            } else {
                return 'Нет статей';
            }
        } else {
            return 'Указанной вики не сущесвует';
        }
    }
    public function update($wikiName, $articleName, Request $request) {
        //dd($articleName);
        $data = request()->validate([
            'title' => 'string',
            'url_title' => 'string',
            'content' => 'string',
        ]);
        //dd($data);
        //Log::info('Updating article', ['data' => $data]);

        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $my_article = [
                'wiki_id' => $wiki->id,
                'url_title' => $data['url_title'],
                'title' => $data['title'],
            ];
            //dd($my_article);
            $articles = Article::where('wiki_id', $wiki->id)->get();
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
                        return 'Ошибка';
                }
            } else {
                return 'Ошибка';
            }

        } else {
            return 'Указанной вики не сущесвует';
        }
    }
    public function destroy($wikiName, $articleName)
    {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            //dd($my_article);
            $articles = Article::where('wiki_id', $wiki->id)->get();
            if ($articles) {

                $my_article2 = $articles->where('url_title', $articleName)->first();

                if ($my_article2) {

                    $my_article2->delete();

                    //return redirect()->route('index.articles', $wiki->url);
                    return "Статья удалена";
                }   else {
                        return 'Ошибка';
                }
            } else {
                return 'Ошибка';
            }

        } else {
            return 'Указанной вики не сущесвует';
        }
        //$article->destroy();
        //return 'Удалено успешно. Надеюсь...';
    }

    public function trash($wikiName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();

            return view('trash', compact('articles', 'wiki'));
        } else {
            return 'Указанной вики не сущесвует';
        }
    }

    public function show_deleted_article($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();

            if($articles) {
                $article = $articles->where('url_title', $articleName)->first();

                if($article) {
                    $revision = DB::table('revisions')->where('article_id', $article->id)->orderBy('id', 'desc')->first();
                    return view('deleted-article', compact('revision', 'wiki', 'article'));
                }
                 else {
                    return 'Такой статьи нет';
                 }
            } else {
                return 'Нет статей';
            }
        } else {
            return 'Указанной вики не сущесвует';
        }
    }

    public function restore($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            //dd($my_article);
            $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();
            //dd($articles);
            if ($articles) {

                $my_article2 = $articles->where('url_title', $articleName)->first();
                //dd($my_article2);
                if ($my_article2) {

                    $my_article2->restore();

                    //dd($my_article2);

                    //return redirect()->route('articles.show', [$wiki->url, $my_article2->url]);
                    return 'Статья восстановлена!';
                }   else {
                        return 'Ошибка';
                }
            } else {
                return 'Ошибка';
            }

        } else {
            return 'Указанной вики не сущесвует';
        }
    }

}
