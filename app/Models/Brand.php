<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
  protected $fillable = ['name','logo'];

  public function types() 
  {
    return $this->belongsToMany(Type::class,'brand_types','brand_id','type_id');

  }
}
