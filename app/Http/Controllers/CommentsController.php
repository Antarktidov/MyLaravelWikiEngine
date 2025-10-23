<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wiki;
use App\Models\Article;
use App\Models\Comment;
use App\Models\CommentRevision;

class CommentsController extends Controller
{
    public function show_comments_under_article(string $wikiName, string $articleName)
    {
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();
        if ($wiki) {
            $articles = Article::where('wiki_id', $wiki->id)->whereNull('deleted_at')->get();

            if($articles) {
                $article = $articles->where('url_title', $articleName)->first();

                if($article) {
                    $comments = Comment::whereNull('deleted_at')
                    ->where('article_id', $article->id)
                    ->paginate(10);

                    $output_comments = [];

                    foreach ($comments as $comment) {
                        $user = User::find($comment->user_id);

                        if ($user != null) {
                            $output_comments[]['user_name'] = $user->name;
                        } else {
                            $output_comments[]['user_name'] = 'Анонимный участник';
                        }

                        $output_comments[]['user'] = User::find($comment->user_id);
                        $output_comments[]['created_at'] = $comment->created_at;

                        $comment_revision = CommentRevision::where('comment_id', $comment->id)
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'desc')->first();

                        $output_comments[]['content'] = $comment_revision->content;
                    }

                    return $output_comments;

                    //return $comments;
                    /*$comment_revision = CommentRevision::where('comment_id', $article->id)
                    //->where('deleted_at', '')
                    ->whereNull('deleted_at')
                    ->orderBy('id', 'desc')->first();
                    if ($revision) {
                        return view('article', compact('revision', 'wiki', 'article'));
                    } else {
                        return response(__('All edits of this article are hidden'), 404)
                            ->header('Content-Type', 'text/plain');
                    }*/
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
}
