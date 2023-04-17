<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $bookName = $this->faker->domainWord();
        // dd($this->getBookThumbnail(Str::slug($bookName)));
        return [
            'title' => $bookName,
            'slug' => Str::slug($bookName),
            'isbn' => $this->faker->numberBetween(10000000, 99999999),
            'publication_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'cover_image' => $this->getBookThumbnail(Str::slug($bookName)),
            'description' => $this->faker->sentence($nbWords = 30, $variableNbWords = true),
            'book' => "https://www.africau.edu/images/default/sample.pdf",
            'author_id' => Author::inRandomOrder()->pluck('id')->first(),
            'genre_id' => Genre::inRandomOrder()->pluck('id')->first(),
            'is_popular' => false,
        ];
    }

    public function getBookThumbnail($slug)
    {
        try {
            $thumbail = "http://placeimg.com/480/640/any";
            $contents = file_get_contents($thumbail);
            $name = substr($thumbail, strrpos($thumbail, '/') + 1);
            $extension = File::extension($name) != "" ?  File::extension($name) : 'png';
            $filename = $slug . '.' . $extension;
            if (!Storage::disk('public')->has('book-thumbnail/' . $filename)) {
                Storage::disk('public')->put('book-thumbnail/' . $filename, $contents);
            }
            return  'book-thumbnail/' . $filename;
        } catch (\Exception $e) {
            return $thumbail;
        }
    }
}
