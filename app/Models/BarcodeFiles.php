<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeFiles extends Model
{
    use HasFactory;
    public $table = "barcode";
    protected $fillable = [
        'npk',
        'barcode_data',
        'barcode_name',
        'barcode_path',
    ];
}
