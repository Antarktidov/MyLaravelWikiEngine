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

                        return 'Правка скрыта';
                    } else {
                        return 'Ошибка';
                    }
                    
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
                        return 'Правка вогсстановлена';
                    } else {
                        return 'Ошибка';
                    }           
                }   else {
                        return 'Ошибка';
                }
            } else {
                return 'Ошибка';
            }

        } else {
            return 'Указанной вики не существует';
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
                            return '404. Введён неверный id правки';
                        }
                    } else {
                        return 'Ошибка';
                    }
                    
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
