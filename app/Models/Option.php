<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class Option extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = false;

    public static function getOptions()
    {
        // Если таблица ещё не создана (во время установки/миграций) —
        // не трогаем БД и отдаем безопасный объект по умолчанию.
        if (!Schema::hasTable((new self)->getTable())) {
            return (object) [
                'id' => 1,
                'protection_level' => 'public',
                'is_comments_enabled' => true,
                'is_registration_enabled' => true,
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ];
        }

        try {
            $options = self::first();
        } catch (QueryException $e) {
            // Например, если вдруг таблица всё-таки отсутствует в конкретной БД.
            return (object) [
                'id' => 1,
                'protection_level' => 'public',
                'is_comments_enabled' => true,
                'is_registration_enabled' => true,
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ];
        }

        if (!$options) {
            $options = self::create([
                'protection_level' => 'public',
                'is_comments_enabled' => true,
                'is_registration_enabled' => true,
            ]);
        }

        return $options;
    }
}
