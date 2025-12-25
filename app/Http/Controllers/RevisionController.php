<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Revision;
use App\Models\User;
use App\Models\Wiki;

class RevisionController extends Controller
{
    //DELETE-ручка для сокрытия правки
    public function destroy(string $wikiName, string $articleName, int $revisionId)
    {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            if ($articles) {

                $my_article = $articles->where('url_title', $articleName)->first();

                if ($my_article) {
                    $revisions = Revision::where('article_id', $my_article->id)->whereNull('deleted_at')->get();

                    if ($revisions) {
                        $my_revision = $revisions->where('id', $revisionId)->first();
                        $my_revision->delete();

                        return response(__('The edit was hidden'), 200)
                            ->header('Content-Type', 'text/plain');
                    } else {
                        return response(__('Error'), 500)
                            ->header('Content-Type', 'text/plain');
                    }

                }   else {
                        return response(__('No such articles'), 404)
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

    //POST-ручка для восстановления скрытой правки
    public function restore(string $wikiName, string $articleName, int $revisionId) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            if ($articles) {
                $my_article = $articles->where('url_title', $articleName)->first();
                if ($my_article) {
                    $revisions = Revision::onlyTrashed()->where('article_id', $my_article->id)->get();
                    if ($revisions) {
                        $my_revision = $revisions->where('id', $revisionId)->first();
                        $my_revision->restore();
                        return response(__('The edit has been restored'), 200)
                            ->header('Content-Type', 'text/plain');
                    } else {
                        return response(__('Error'), 500)
                            ->header('Content-Type', 'text/plain');
                    }
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

    //Просмотр правки статьи по ID
    public function view(string $wikiName, string $articleName, int $revisionId)
    {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            if ($articles) {

                $article = $articles->where('url_title', $articleName)->first();

                if ($article) {
                    $revisions = Revision::where('article_id', $article->id)->whereNull('deleted_at')->get();

                    if ($revisions) {
                        $revision = $revisions->where('id', $revisionId)->first();

                        if($revision) {
                            return view('revision', compact('revision', 'wiki', 'article'));
                        }
                        else {
                            return response(__('404. Invalid edit id entered.'), 404)
                                ->header('Content-Type', 'text/plain');
                        }
                    } else {
                        return response(__('Error'), 500)
                            ->header('Content-Type', 'text/plain');
                    }

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

    //Показывает историю страницы
    public function index(string $wikiName, string $articleName) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();
            if($articles) {
                $article = $articles->where('url_title', $articleName)->first();
                if($article) {
                    $revisions = Revision::whereNull('deleted_at')->get();
                    $users = User::all();
                    return view('history', compact('article', 'revisions',
                        'users', 'wiki'));
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

    //Показывает историю удалённой страницы
    //(требуются технические права)
    public function show_deleted_hist(string $wikiName, string $articleName) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
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

    //Показывает скрытые правки в истории страницы
    //(требуются технические права)
    public function trash(string $wikiName, string $articleName) {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $article = Article::where('wiki_id', $wiki->id)->where('url_title', $articleName)->whereNull('deleted_at')->first();
            if ($article) {
                $revisions = Revision::onlyTrashed()->where('article_id', $article->id)->get();
                $users = User::all();
                return view('deleted_history', compact('article', 'revisions', 'users', 'wiki'));
            } else {
                return response(__('Article does not exist'), 404)
                    ->header('Content-Type', 'text/plain');
            }
        } else {
            return response(__('Wiki does not exist'), 404)
                ->header('Content-Type', 'text/plain');
        }
    }
}
