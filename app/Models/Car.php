<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
  protected $fillable = ['carmodel_id','plate_number','status','image','color'];
  
  public function carModel()
  {
    return $this->belongsTo(CarModel::class);
  }
  public function bookings()
  {
    return $this->hasMany(Booking::class);
  }

}
