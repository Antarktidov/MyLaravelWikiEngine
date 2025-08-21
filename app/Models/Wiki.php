<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
