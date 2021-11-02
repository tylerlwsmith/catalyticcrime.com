<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function url()
    {
        return Storage::disk(env('UPLOADS_FILESYSTEM'))->url($this->path);
    }

    public function getUrlAttribute()
    {
        return $this->url();
    }
}
