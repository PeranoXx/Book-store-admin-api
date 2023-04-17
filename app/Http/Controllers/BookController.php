<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $resposes = [
            'status' => '',
            'message' => '',
            'data' => '',
        ];
        try {
            $limit = isset($request->limit) ? $request->limit : 10;  //data limit
            $field = isset($request->field) ? $request->field : 'id'; // sortable field, id not defined take id as default
            $dir = isset($request->dir) ? $request->dir : 'DESC'; // sortable direction, if not defined take DESC as default
            $author = isset($request->author) ? explode(',', $request->author) : ''; // filter by author, give author id
            $genre = isset($request->genre) ? explode(',', $request->genre) : ''; // filter by genre give genre id
            $search = isset($request->search) ? $request->search : ''; // search for name , slug or isbn
            $pub_date_from = isset($request->pub_date_from) ? $request->pub_date_from : ''; //date from
            $pub_date_to = isset($request->pub_date_to) ? $request->pub_date_to : ''; // date to

            $books = Book::with(['author:id,name,email,phone', 'genre:id,name']);

            if (!empty($search)) {
                $books = $books->where(function ($books) use ($search) {
                    return $books->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            }

            if (!empty($author)) {
                $books = $books->whereIn('author_id', $author);
            }

            if (!empty($genre)) {
                $books = $books->whereIn('genre_id', $genre);
            }

            if (!empty($pub_date_from) && !empty($pub_date_to)) {
                $books = $books->whereBetween('publication_date', [$pub_date_from, $pub_date_to]);
            }

            $books = $books->orderBy($field, $dir)->paginate($limit);

            $authors = Author::select('id', 'name')->get();
            $genre = Genre::select('id', 'name')->get();
            $data = [
                'books' => $books,
                'authors' => $authors,
                'genre' => $genre,
            ];
            $resposes = [
                'status' => 'success',
                'message' => 'books fetched successfully.',
                'data' => $data,
            ];
            return $resposes;
        } catch (\Exception $e) {
            return getResponse($e);
        }
    }

    public function get(Request $request, $slug)
    {
        $resposes = [
            'status' => '',
            'message' => '',
            'data' => '',
        ];

        try {
            $book = Book::where('slug', $slug)->with(['author:id,name,email,phone', 'genre:id,name'])->first();
            if (!$book) {
                $resposes = [
                    'status' => 'error',
                    'message' => 'Book not exist!',
                    'data' => '',
                ];
                return $resposes;
            }
            $relatedAuthor = Book::where('author_id', $book->author_id)->inRandomOrder()->with(['genre:id,name'])->take(3)->get();
            $relatedGenre = Book::where('genre_id', $book->genre_id)->inRandomOrder()->with(['genre:id,name'])->take(3)->get();

            $data = [
                'book' =>  $book,
                'related_author' =>  $relatedAuthor,
                'related_genre' =>  $relatedGenre,
            ];

            $resposes = [
                'status' => 'success',
                'message' => 'book fetch sucessfully!',
                'data' => $data,
            ];
            return $resposes;
        } catch (\Exception $e) {
            return getResponse($e);
        }
    }
}
