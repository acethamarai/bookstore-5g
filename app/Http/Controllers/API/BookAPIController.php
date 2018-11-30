<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Book;
use Validator;
use DB;
use App\Http\Controllers\Controller as Controller;

class BookAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        //$books = Book::all();
		$lastId = $request->input('lastId');
		if($lastId == "" || $lastId == NULL){
		$lastId = Book::orderBy('id', 'desc')->first()->id;
		}
		$books = DB::table('books')
            ->leftJoin('authors', 'books.author_id', '=', 'authors.id')
			->select('books.id','books.slug','books.title','books.price', 'books.image', 'authors.name')
			->where('books.id', '<=', $lastId )
			->orderby('books.id', 'desc')
            ->limit(10)
            ->get();		
		//dd($books);

		$response = [
            'success' => true,
            'books'    => $books,
            'datacounts' => $lastId,
        ];
        return response()->json($response, 200);
    }
	/**
     * Display a single resource information.
     *
     */
    public function show(Request $request)
    {
		$id = $request->input('id');
        $book = DB::table('books')
            ->leftJoin('authors', 'books.author_id', '=', 'authors.id')
			->select('books.id','books.slug','books.title','books.description', 'books.price', 'books.image', 'authors.name')
			->where('books.id', '=', $id )
            ->first();
        $response = [
            'success' => true,
            'book'    => $book
        ];
        return response()->json($response, 200);
    }
	
	public function slug($slug)
    {
        $book = DB::table('books')
            ->leftJoin('authors', 'books.author_id', '=', 'authors.id')
			->select('books.id','books.slug','books.title','books.description', 'books.price', 'books.image', 'authors.name')
			->where('books.slug', '=', $slug )
            ->first();
        $response = [
            'success' => true,
            'book'    => $book
        ];
        return response()->json($response, 200);
    }
   
}