<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanteenTwo extends Model
{
    use HasFactory;
    public $table = "canteen_twos";
    protected $fillable = [
        'canteen_no',
        'npk',
        'name',
        'dept',
        'date',
    ];
}
