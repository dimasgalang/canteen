<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuplicateScanner extends Model
{
    use HasFactory;

    protected $table = 'duplicate_scanner';

    protected $fillable = [
        'npk',
        'name',
        'already_scan_canteen_number',
        'need_to_scan_canteen_number',
        'date',
    ];
}
