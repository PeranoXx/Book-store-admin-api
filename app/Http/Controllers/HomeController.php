<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function index(Request $request){
        $resposes = [
            'status' => '',
            'message' => '',
            'data' => '',
        ];

        try {
            $popularAuthorBooks = Cache::rememberForever('popularAuthorBooks', function () {
                return Author::select('id', 'name', 'email')->where('is_popular', true)->get();
                dd(DB::getQueryLog());
            });
            if(count($popularAuthorBooks) > 0){
                foreach($popularAuthorBooks as $key => $author){
                    $popularAuthorBooks[$key]->books = $author->books->map(function($value) use ($popularAuthorBooks, $key){
                        $value->genre = $value->genre;
                    });
                }
            }
    
            $popularBooks = Cache::rememberForever('popularBooks', function () {
                return Book::select('id', 'title', 'slug', 'isbn','publication_date','cover_image','author_id', 'genre_id')->where('is_popular', true)->with(['author:id,name,email','genre:id,name'])->get();
            });
            $data = [
                'popular_books' => $popularBooks->toArray(),
                'popular_author' => $popularAuthorBooks->toArray(),
            ];

            $resposes = [
                'status' => 'success',
                'message' => 'Data Fetch successfullly!',
                'data' => $data,
            ];
            return response($resposes, 200);
        } catch (\Exception $e) {
            return getResponse($e);
        }
    }

    public function search(Request $request){
        $resposes = [
            'status' => '',
            'message' => '',
            'data' => '',
        ];

        try {

            $search = isset($request->search) ? $request->search : ''; // search for name , slug or isbn

            if(!empty($search)){
                $books = Book::select('id', 'title', 'slug', 'cover_image', 'author_id')->with(['author:id,name'])->where('title', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")->paginate(5);

              
            }else{
                $books = Book::select('id', 'title', 'slug', 'cover_image', 'author_id')->with(['author:id,name'])->where('is_popular', true)->paginate(5);
            }

            $resposes = [
                'status' => 'success',
                'message' => 'searched result',
                'data' => $books,
            ];

            return response($resposes, 200);
        } catch (\Exception $e) {
            return getResponse($e);
        }
        
    }
}
