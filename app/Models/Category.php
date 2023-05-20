<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function setNameAttribute($value){
        return $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value){
        return $this->attributes['name'] = ucwords($value);
    }
}
