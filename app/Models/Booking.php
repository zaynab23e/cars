<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
  protected $fillable = ['car_id','user_id','driver_id','start_date','end_date','final_price','status']; 

  public function user()
  {
    return $this->belongsTo(User::class);
  }
  public function car()
  {
    return $this->belongsTo(Car::class);
  }
  public function driver()
  {
    return $this->belongsTo(Driver::class);
  }

}
