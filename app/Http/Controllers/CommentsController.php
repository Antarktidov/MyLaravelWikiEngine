<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wiki;
use App\Models\Article;
use App\Models\Comment;
use App\Models\CommentRevision;

use Illuminate\Support\Str;

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
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

                    $output_comments = [];

                    foreach ($comments as $comment) {
                        $user = User::find($comment->user_id);
                        $user_name = $user ? $user->name : 'Анонимный участник';

                        $comment_revision = CommentRevision::where('comment_id', $comment->id)
                            ->whereNull('deleted_at')
                            ->orderBy('id', 'desc')
                            ->first();

                        $content = $comment_revision ? $comment_revision->content : null;

                        $output_comments[] = [
                            'id' => $comment->id,
                            'user_id' => $comment->user_id,
                            'user_name' => $user_name,
                            'created_at' => $comment->created_at ? $comment->created_at->format('Y-m-d H:i:s') : null,
                            'content' => Str::of($content)->markdown([
                                'html_input' => 'strip',
                            ]),
                        ];
                    }

                    return response()->json([
                        'data' => $output_comments,
                        'meta' => [
                            'current_page' => $comments->currentPage(),
                            'per_page' => $comments->perPage(),
                            'total' => $comments->total(),
                            'last_page' => $comments->lastPage(),
                        ],
                    ]);
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

    public function store(string $wikiName, string $articleName, Request $request) {
        $data = request()->validate([
            'content' => 'string',
        ]);
        $wiki = Wiki::where('url', $wikiName)->whereNull('deleted_at')->first();

        if ($wiki) {
            $article = Article::where('wiki_id', $wiki->id)
            ->where('url_title', $articleName)
            ->whereNull('deleted_at')
            ->first();

            if ($article) {
                $user = auth()->user();

                $userId = 0;
                if ($user != null) {
                    $userId = $user->id;
                }

                $comment = [
                    'user_id' => $userId,
                    'user_ip' => $request->ip(),
                    'article_id' => $article->id,
                ];

                $created_comment = Comment::create($comment);

                $comment_revision = [
                    'content' => $data['content'],
                    'user_ip' => $request->ip(),
                    'comment_id' => $created_comment->id,
                ];

                CommentRevision::create($comment_revision);

                return ['message' => 'comment_posted'];
            }
        }
    }
}