<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['name','description'];
    
    public function brands() 
    {
        return $this->belongsToMany(Brand::class,'brand_types','type_id','brand_id');

    }
    public function carModels() 
    {
        return $this->hasMany(CarModel::class);

    }
}
