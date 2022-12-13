<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookDownloads;
use App\Models\BookReviews;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    //traer listado de todos los libros
    public function index(){
        //llama al controller, en la funcion getResponseSuccess
       // $response = $this->getResponseSuccess();
        //se obtiene todos los registros
        //$book = Book::all();
        //$book = Book::with('category','editorial','authors')->get();
        //se asignan los registros al data del response
        //$response["data"] = $book;
        //se retorna el response
        //return $response;
        //php artisan

        //$books = Book::orderBy('title','asc')->get();
        $books = Book::with('category','editorial','authors','bookDownloads')->get();
        return $this->getResponse200($books);
    }

    public function show($id){

        if(Book::where('id',$id)->exists()){
            $book = Book::with('category','editorial','authors','bookDownloads')->get()->where('id',$id);
        return $this->getResponse200($book);
        }else{
            return $this->getResponse404();
        }

    }

    public function store(Request $request)
    {
       // $isbn = preg_replace('/\s+/', '\u0020', $request->isbn); //Remove blank spaces from ISBN
        try {
            DB::beginTransaction();
            $existIsbn = Book::where("isbn", trim($request->isbn))->exists(); //Check if a registered book exists (duplicate ISBN)
            if (!$existIsbn) { //ISBN not registered
                $book = new Book();
                $book->isbn = trim($request->isbn);
                $book->title = $request->title;
                $book->description = $request->description;
                $book->published_date = date('y-m-d h:i:s'); //Temporarily assign the current date
                $book->category_id = $request->category["id"]; //recibir un objeto en el body de la peticion
                $book->editorial_id = $request->editorial["id"];
                $book->save();
                foreach ($request->authors as $item) { //Associate authors to book (N:M relationship)
                    $book->authors()->attach($item);
                }
                $bookDownload = new BookDownloads();
                $bookDownload->book_id = $book->id;
                $bookDownload->save();
                DB::commit();
                return $this->getResponse201('book', 'created', $book);
            } else {
                return $this->getResponse500(['The isbn field must be unique']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([$e]);
        }
    }


    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            //$isbn = preg_replace('/\s+/', '\u0020', $request->isbn); //Remove blank spaces from ISBN
            $existBook = Book::where("id", $id)->exists(); //Check if a registered book exists (duplicate ISBN)

            if ($existBook) { //ISBN not registered
                $book = Book::get()->where("id", $id)->first();
                $book->isbn = trim($request->isbn);
                $book->title = $request->title;
                $book->description = $request->description;
                $book->published_date = date('y-m-d h:i:s'); //Temporarily assign the current date
                $book->category_id = $request->category["id"]; //recibir un objeto en el body de la peticion
                $book->editorial_id = $request->editorial["id"];
                $book->update();
                foreach ($book->authors as $item) { //deissasociate authors to book (N:M relationship)
                    $book->authors()->detach($item->id);
                }
                foreach ($request->authors as $item) { //deissasociate authors to book (N:M relationship)
                    $book->authors()->attach($item);
                }
                $book = Book::with('category','editorial','authors')->where("id",$id)->get();
                DB::commit();
                return $this->getResponse201('book', 'updated', $book);
            } else {
                return $this->getResponse500(['The book is not registered']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([$e]);
        }
    }

    public function destroy($id){
        $book = Book::get()->where('id',$id)->first();//al solo obtener un registro, usar first
            if ($book != null) {
                foreach($book->authors as $author){
                    $book->authors()->detach($author->id);
                }
               // $bookDownload->book_id = $book->id;

                $book->bookDownloads()->delete();
                $book->delete();
                return $this->getResponseDelete200('book');
            } else {
                return $this->getResponse404();
            }
}

public function addBookReview(Request $request,$book_id){
    $validator = Validator::make($request->all(), [
        'comment' => 'required'
    ]);
        if (!$validator->fails()) {
            DB::beginTransaction();
            try{
                $user = auth()->user();
                $bookRew = new BookReviews();
                $bookRew->comment = $request->comment;
                $bookRew->book_id = $book_id;
                $bookRew->user_id = $user->id;
                $bookRew->save();
                $bookRew = BookReviews::with('books','users')->get()->where('id',$bookRew->id);
                DB::commit();
                return $this->getResponse201('book review','created',$bookRew);
            }catch (Exception $e){
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        } else {
            return $this->getResponse500([$validator->errors()]);
        }
}


public function updateBookReview(Request $request,$id){
    $validator = Validator::make($request->all(), [
        'comment' => 'required'
    ]);
        if (!$validator->fails()) {
            DB::beginTransaction();
            try{
                $bookRew = BookReviews::where('id',$id)->get()->first();
                $user = auth()->user();
                if($bookRew->user_id != $user->id){
                    return $this->getResponse403();
                }else{
                    $bookRew->comment = $request->comment;
                    $bookRew->edited = true;
                    $bookRew->update();
                    $bookRew = BookReviews::with('books','users')->get()->where('id',$id);
                    DB::commit();
                    return $this->getResponse201('book review','updated',$bookRew);
                }

            }catch (Exception $e){
                DB::rollBack();
                return $this->getResponse500([$e->getMessage()]);
            }
        } else {
            return $this->getResponse500([$validator->errors()]);
        }
}


}

