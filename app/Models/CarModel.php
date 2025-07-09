<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'carmodels';
    protected $hidden = ['created_at', 'updated_at'];


    protected $fillable = [
        'year',
        'count', 
        'price', 
        'image',
        'model_name_id',
        'engine_type',
        'transmission_type',
        'seat_type',
        'seats_count',
        'acceleration',
    ];

    public function modelName()
    {
        return $this->belongsTo(ModelName::class, 'model_name_id');
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

  
}
