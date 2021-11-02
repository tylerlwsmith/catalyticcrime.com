<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $address
 */
class Report extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'user_id'];

    protected $dates = ['date', 'time'];

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'code', 'vehicle_code');
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function getAddressAttribute()
    {
        if ($this->street_address_2) {
            return "$this->street_address_1 $this->street_address_2, Bakersfield, CA $this->zip";
        }
        return "$this->street_address_1, Bakersfield, CA $this->zip";
    }
}
