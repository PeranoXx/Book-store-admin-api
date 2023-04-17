<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Author extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function books()
    {
        return $this->hasMany(Book::class)->select('id', 'title', 'slug', 'isbn','publication_date','cover_image', 'description', 'author_id', 'genre_id')->limit(3);
    }

    public static function booted()
    {

        self::saved(function ($model) {
            Cache::forget('popularAuthorBooks');
        });

        self::created(function ($model) {
            Cache::forget('popularAuthorBooks');
        });
        self::deleted(function ($model) {
            Cache::forget('popularAuthorBooks');
        });
    }
}
