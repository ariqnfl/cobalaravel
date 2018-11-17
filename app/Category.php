<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use SoftDeletes;

    public function books(){
        return $this->belongsToMany('App\Book');
    }
    protected $fillable = [
        'name', 'slug', 'image', 'created_by'
    ];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value, '-');
    }
}
