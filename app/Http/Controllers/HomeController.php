<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\LogCiiper;
use App\Models\OrderMaster;
use App\Models\ProductionPlanning;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use IcehouseVentures\LaravelChartjs\Facades\Chartjs;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        // dd($totaluser);
        // return view('home', compact('totalapproved','totalpending','totaldocument','totaluser'));
        return view('home');
    }
}
