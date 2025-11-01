<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syslog extends Model
{
    use HasFactory;

     protected $fillable = [
        'username',
        'activity',
        'menu',
        'log_date',
        'ip_address',
        'mac_address',
        'browser_type',
        'os',
    ];
}
