<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }
    public function orders(){
        return $this->belongsToMany('App\Order');
    }

    protected $fillable = [
        'title', 'description', 'author', 'publisher', 'price', 'stock', 'status', 'slug', 'cover', 'created_by'
    ];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value, '-');
    }
}
