<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Wiki;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    public function wiki()
    {
        return $this->belongsTo(Wiki::class);
    }
    public function article()
    {
        $this->hasMany(Revision::class);
    }

}
