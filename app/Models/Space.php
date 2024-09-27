<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'type',
        'image_url',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
