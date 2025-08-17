<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Revision;
use Illuminate\Support\Facades\DB;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ArticleController extends Controller
{
    //Заглавная конкретной вики: список всех статей
    //(Аналог Служебная:Все страницы)
    public function index(string $wikiName): string|View {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();

            return view('show-all-articles', compact('articles', 'wiki'));
        } else {
            return __('Wiki does not exist');
        }
    }

    //Показывает вики-страницу
    public function show(string $wikiName, string $articleName): string|View {
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
                        return __('All edits of this article are hidden');
                    }
                }
                 else {
                    return __('Article does not exist');
                 }
            } else {
                return __('No articles');
            }
        } else {
            return __('Wiki does not exist');;
        }
    }

    //Форма создания статьи
    public function create(string $wikiName): string|View {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            return view('create-article', compact('wiki'));
        } else {
            return __('Wiki does not exist');;
        }
    }

    //POST-ручка для создания статьи
    public function store(string $wikiName, Request $request): string|Redirect|RedirectResponse {
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
            return __('Wiki does not exist');
        }
    }

    //Форма правки статьи
    public function edit(string $wikiName, string $articleName): string|View {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();
            if ($articles) {
                $article = $articles->where('url_title', $articleName)->first();
                if ($article) {
                    $revision = DB::table('revisions')->where('article_id', $article->id)->orderBy('id', 'desc')->first();
                    return view('edit', compact('article', 'revision', 'wiki'));
                } else {
                    return __('Article does not exist');
                }

            } else {
                return __('No articles');
            }
        } else {
            return __('Wiki does not exist');;
        }
    }

    //POST-ручка для формы правки статьи
    public function update($wikiName, $articleName, Request $request): string|Redirect|RedirectResponse
    {
        $data = request()->validate([
            'title' => 'string',
            'url_title' => 'string',
            'content' => 'string',
        ]);

        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $my_article = [
                'wiki_id' => $wiki->id,
                'url_title' => $data['url_title'],
                'title' => $data['title'],
            ];
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
                        return __('Error');
                }
            } else {
                return __('Error');
            }

        } else {
            return __('Wiki does not exist');
        }
    }

    //DELETE-ручка для удаления статьи
    //(требуются технические права)
    public function destroy(string $wikiName, string $articleName): string
    {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();
            if ($articles) {

                $my_article2 = $articles->where('url_title', $articleName)->first();

                if ($my_article2) {

                    $my_article2->delete();
                    return __('Article was deleted');
                }   else {
                        return __('Error');
                }
            } else {
                return __('Error');
            }

        } else {
            return __('Wiki does not exist');
        }
    }

    //Список удалённых статей
    //(требуются технические права)
    public function trash(string $wikiName): string|View {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();

            return view('trash', compact('articles', 'wiki'));
        } else {
            return __('Wiki does not exist');
        }
    }

    //Просмотр удалённой статьи
    //(требуются технические права)
    public function show_deleted(string $wikiName, string $articleName): string|View {
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
                    return __('Article does not exist');
                 }
            } else {
                return __('No articles');
            }
        } else {
            return __('Wiki does not exist');
        }
    }

    //POST-ручка для восстановления стаьи
    //(требуются технические права)
    public function restore(string $wikiName, string $articleName): string {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::onlyTrashed()->where('wiki_id', $wiki->id)->get();
            if ($articles) {

                $my_article2 = $articles->where('url_title', $articleName)->first();
                if ($my_article2) {

                    $my_article2->restore();
                    return __('Article was restored');
                }   else {
                        return __('Error');
                }
            } else {
                return __('Error');
            }

        } else {
            return __('Wiki does not exist');
        }
    }

}
