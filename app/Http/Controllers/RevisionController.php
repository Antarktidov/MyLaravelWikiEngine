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

class RevisionController extends Controller
{
    //DELETE-ручка для сокрытия правки
    public function destroy($wikiName, $articleName, $revisionId)
    {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();
            if ($articles) {

                $my_article = $articles->where('url_title', $articleName)->first();

                if ($my_article) {
                    $revisions = Revision::where('article_id', $my_article->id)->get();

                    if ($revisions) {
                        $my_revision = $revisions->where('id', $revisionId)->first();
                        $my_revision->delete();

                        return __('The edit was hidden');
                    } else {
                        return __('Error');
                    }

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

    //POST-ручка для восстановления скрытой правки
    public function restore($wikiName, $articleName, $revisionId) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();
            if ($articles) {
                $my_article = $articles->where('url_title', $articleName)->first();
                if ($my_article) {
                    $revisions = Revision::onlyTrashed()->where('article_id', $my_article->id)->get();
                    if ($revisions) {
                        $my_revision = $revisions->where('id', $revisionId)->first();
                        $my_revision->restore();
                        return __('The edit has been restored');
                    } else {
                        return __('Error');
                    }
                }   else {
                        return __('Error');
                }
            } else {
                return __('Error');
            }

        } else {
            return __('Wiki does not exist');;
        }
    }

    //Просмотр правки статьи по ID
    public function view($wikiName, $articleName, $revisionId)
    {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->get();
            if ($articles) {

                $article = $articles->where('url_title', $articleName)->first();

                if ($article) {
                    $revisions = Revision::where('article_id', $article->id)->get();

                    if ($revisions) {
                        $revision = $revisions->where('id', $revisionId)->first();

                        if($revision) {
                            return view('revision', compact('revision', 'wiki', 'article'));
                        }
                        else {
                            return __('404. Invalid edit id entered.');
                        }
                    } else {
                        return __('Error');
                    }

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

    //Показывает историю страницы
    public function index($wikiName, $articleName) {
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
                    return __('Article does not exist');
                }
            } else {
                return __('No articles');
            }
        } else {
            return __('Wiki does not exist');;
        }
    }

    //Показывает историю удалённой страницы
    //(требуются технические права)
    public function show_deleted_hist($wikiName, $articleName) {
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
                    return __('Article does not exist');
                }
            } else {
                return __('No articles');
            }
        } else {
            return __('Wiki does not exist');;
        }
    }

    //Показывает скрытые правки в истории страницы
    //(требуются технические права)
    public function trash($wikiName, $articleName) {
        $wiki = DB::table('wikis')->where('url', $wikiName)->first();
        if ($wiki) {
            $article = Article::where('wiki_id', $wiki->id)->where('url_title', $articleName)->first();
            if ($article) {
                $revisions = Revision::onlyTrashed()->where('article_id', $article->id)->get();
                $users = User::all();
                return view('deleted_history', compact('article', 'revisions', 'users', 'wiki'));
            } else {
                return __('Article does not exist');
            }
        } else {
            return __('Wiki does not exist');;
        }
    }
}
