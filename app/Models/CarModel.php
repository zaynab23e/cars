<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'carmodels';

    protected $fillable = ['name', 'year', 'count', 'price', 'type_id'];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

  
}
