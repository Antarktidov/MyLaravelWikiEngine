<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfileRevision extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'avatar',
        'banner',
        'about',
        'aka',
        'i_live_in',
        'discord',
        'discord_if_bot',
        'vk',
        'telegram',
        'github',
        'wiki_id',
        'user_id',
        'is_approved',
    ];
}
