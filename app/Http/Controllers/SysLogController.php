<?php

namespace App\Http\Controllers;

use App\Models\SysLog;
use Illuminate\Http\Request;

class SysLogController extends Controller
{
    public function index() 
    {
        $sysLogs   = SysLog::orderBy('created_at', 'desc')->get();
        return view('syslog.index', compact('sysLogs'));
    }
}
