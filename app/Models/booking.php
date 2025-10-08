<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    
    protected $guarded = [];
    protected $casts = ['from_date' => 'date', 'to_date' => 'date'];
}