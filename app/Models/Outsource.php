<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outsource extends Model
{
    use HasFactory;

    protected $fillable = [
        'NPK',
        'NAMA',
        'VENDOR',
        'void',
    ];

    protected $table = 'outsources';
}
