<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    static function getOptions() {
        if (self::count() > 0) {
            return self::first();
        } else {
            return (object) [
                "id" => 1,
                "protection_level" => "private",
                "is_comments_enabled" => false,
                "is_registration_enabled" => false,
                "created_at" => null,
                "updated_at" => null,
                "deleted_at" => null,
            ];
        }
    }
}
