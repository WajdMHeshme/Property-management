<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Builder\Property;

class Booking extends Model
{
    protected $fillable =
    [
        'user_id',
        'property_id',
        'scheduled_at',
        'status',
        'notes',
    ];
    public function customer()
    {
        $this->belongsTo(User::class, 'user_id');
    }
    public function property()
    {
        return $this->belongsTo(Property::class ,'property-id');
    }
    public function employee(){
        return $this->belongsTo(User::class,'employee_id');
    }
}
