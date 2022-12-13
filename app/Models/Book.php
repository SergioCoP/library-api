<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $table = "books";
    protected $fillable = ['id','isbn','title','description','published_date','category_id','editorial_id'];
    public $timestamps = false;

    public function bookDownloads(){
        return $this->hasOne(BookDownloads::class);
    }

    //retornar los valores relaciones con las otras tablas
    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function editorial(){
        return $this->belongsTo(Editorial::class,'editorial_id','id');
    }

    //faltan autores de belongsToMany
    public function authors(){
        return $this->belongsToMany(
            Author::class, //tabla de relacion
            'authors_books', //tabla pivote o interseccion
            'books_id', //from
        'authors_id'); //to
    }
}

