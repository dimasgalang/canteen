<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canteen extends Model
{
    use HasFactory;
    public $table = "canteen";
    protected $fillable = [
        'canteen_no',
        'npk',
        'name',
        'date',
    ];
}
