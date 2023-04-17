<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function genre(){
        return $this->belongsTo(Genre::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    protected function coverImage(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Storage::disk('public')->url($value),
        );
    }

    public static function booted()
    {
        self::saved(function ($model) {
            Cache::forget('popularBooks');
        });

        self::created(function ($model) {
            Cache::forget('popularBooks');
        });
        self::deleted(function ($model) {
            Cache::forget('popularBooks');
        });
    }
}
