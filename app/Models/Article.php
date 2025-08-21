<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    public function wiki()
    {
        return $this->belongsTo(Wiki::class, 'wiki_id', 'id');
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class, 'article_id', 'id');
    }

}
