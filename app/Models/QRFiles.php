<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRFiles extends Model
{
    use HasFactory;
    public $table = "qrcode";
    protected $fillable = [
        'npk',
        'qr_data',
        'qr_name',
        'qr_path',
    ];
}
