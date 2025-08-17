<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Revision extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}
