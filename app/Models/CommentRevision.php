<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Comment;

class CommentRevision extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    use HasFactory;

    public function article()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id');
    }
}
