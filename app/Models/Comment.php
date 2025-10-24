<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\CommentRevision;
use App\Models\Article;
use App\Models\User;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    use HasFactory;

    public function comment_revisions(): HasMany
    {
        return $this->hasMany(CommentRevision::class, 'comment_id', 'id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
