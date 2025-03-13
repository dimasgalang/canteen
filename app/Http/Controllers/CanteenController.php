<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use Illuminate\Http\Request;

class CanteenController extends Controller
{
    public function index() {
        $canteens = Canteen::all();
        return view('canteen.index', compact('canteens'));
    }
}
