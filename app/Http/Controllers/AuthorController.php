<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PharIo\Manifest\AuthorElement;
use Illuminate\Support\Facades\DB;

use App\Models\Author;
use Exception;
class AuthorController extends Controller
{
    public function index(){
        $authors = Author::orderBy('name','asc')->get();
        return $this->getResponse200($authors);
    }

    public function show($id){
        if(Author::where("id", $id)->exists()){
            $author = Author::get()->where('id',$id);
             return $this->getResponse200($author);
        }else{
            return $this->getResponse404();
        }


    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $existauthor = Author::where("name", $request->name)->exists() && Author::where("first_surname", $request->first_surname)->exists() && Author::where("second_surname", $request->second_surname)->exists(); //Check if a registered book exists (duplicate ISBN)
            if (!$existauthor) { //ISBN not registered
                $author = new Author();
                $author->name = $request->name;
                $author->firstname = $request->firstname;
                $author->surname = $request->surname;
                $author->save();
                DB::commit();
                return $this->getResponse201('author', 'created', $author);
            } else {
                DB::rollBack();
                return $this->getResponse404();
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
            $existauthor = Author::where("id", $id)->exists();
            if ($existauthor) { //ISBN not registered
                $author = Author::get()->where("id", $id)->first();
                $author->name = $request->name;
                $author->firstname = $request->firstname;
                $author->surname = $request->surname;
                $author->update();
                DB::commit();
                return $this->getResponse201('author', 'updated', $author);
            } else {
                DB::rollBack();
                return $this->getResponse404();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->getResponse500([$e]);
        }
    }

    public function destroy($id){
            $author = Author::get()->where('id',$id)->first();
            if ($author != null) {
                $author->books()->detach();
                $author->delete();
                return $this->getResponseDelete200('book');
            } else {
                return $this->getResponse404();
            }
    }
}
