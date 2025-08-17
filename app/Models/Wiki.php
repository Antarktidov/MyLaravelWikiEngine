<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Article;

class Wiki extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    public function article()
    {
        $this->hasMany(Article::class, 'article_id', 'id');
    }
}
