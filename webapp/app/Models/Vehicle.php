<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $code
 */
class Vehicle extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function getCodeAttribute()
    {
        return "{$this->make}-{$this->model}-{$this->year}";
    }

    public function __toString()
    {
        return "{$this->year} {$this->make} {$this->model}";
    }
}
