<?php

namespace App\Http\Controllers;

use App\Exports\CanteensExport;
use App\Models\Canteen;
use App\Models\CanteenTwo;
use App\Models\Syslog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class CanteenController extends Controller
{
    protected $now;
    protected $dateNow;

    public function __construct()
    {
        $this->now = Carbon::now();
        $this->dateNow = Carbon::today();
    }

    public function index()
    {
        $canteens = Canteen::select('*')->where('date', '=', $this->dateNow->toDateString())->orderByDesc('created_at')->get();
        // dd($canteens);
        return view('canteen.index', compact('canteens'));
    }

    public function scancanteen1()
    {
        $canteens = Canteen::select('*')->where('date', '=', $this->dateNow->toDateString())->where('canteen_no', '=', '1')->orderByDesc('created_at')->get();
        // dd($this->dateNow->toDateString());
        return view('canteen.canteen1', compact('canteens'));
    }

    public function scancanteen2()
    {
        $canteens = CanteenTwo::select('*')->where('date', '>', $this->dateNow->toDateString())->where('canteen_no', '=', '2')->orderByDesc('created_at')->get();
        return view('canteen.canteen2', compact('canteens'));
    }

    public function showcanteen(Request $request)
    {
        if ($request->ajax()) {
            if($request->canteen_no == '1') {
                $canteens = Canteen::take(100);
            } else {
                $canteens = CanteenTwo::take(100);
            }
            // $canteens = Canteen::take(100);
            // dd($canteens);
            return DataTables::of($canteens)
                ->addIndexColumn()
                ->addColumn('created_at_formated', function ($row) {
                    return date('d-m-Y H:i:s', strtotime($row->created_at));
                })
                ->rawColumns(['created_at_formated'])
                ->filter(function ($instance) use ($request) {
                    if ($request->filled('fromdate') && $request->filled('todate')) {
                        // dd($request->get('break'));
                        if ($request->get('break') == 'normal') {
                            $filterfrom = Carbon::parse($request->fromdate . ' 10:00:00')->format('H:i:s');
                            $filterto = Carbon::parse($request->todate . ' 15:59:59')->format('H:i:s');
                            $instance
                                ->where('date', '>=', $request->get('fromdate'))
                                ->where('date', '<=', $request->get('todate'))
                                ->where('canteen_no', '=', $request->get('canteen_no'))
                                ->whereRaw('CAST(created_at AS TIME) BETWEEN ? AND ?', [$filterfrom, $filterto]);
                        } else {
                            $filterfrom = Carbon::parse($request->fromdate . ' 16:00:00')->format('H:i:s');
                            $filterto = Carbon::parse($request->todate . ' 24:00:00')->format('H:i:s');
                            $instance
                                ->where('date', '>=', $request->get('fromdate'))
                                ->where('date', '<=', $request->get('todate'))
                                ->where('canteen_no', '=', $request->get('canteen_no'))
                                ->whereRaw('CAST(created_at AS TIME) BETWEEN ? AND ?', [$filterfrom, $filterto]);
                        }
                    }
                })->make(true);
        };
    }


    public function showcanteen1()
    {
        $canteens = Canteen::orderBy('created_at', 'desc')->where('date', '=', $this->dateNow->toDateString())->where('canteen_no', '=', '1')->get();
        return DataTables::of($canteens)
            ->addIndexColumn()
            ->addColumn('created_at_formated', function ($row) {
                return date('d-m-Y H:i:s', strtotime($row->created_at));
            })
            ->rawColumns(['created_at_formated'])
            ->make(true);
    }


    public function showcanteen2()
    {
        $canteens = CanteenTwo::orderBy('created_at', 'desc')->where('date', '=', $this->dateNow->toDateString())->where('canteen_no', '=', '2')->get();
        return DataTables::of($canteens)
            ->addIndexColumn()
            ->addColumn('created_at_formated', function ($row) {
                return date('d-m-Y H:i:s', strtotime($row->created_at));
            })
            ->rawColumns(['created_at_formated'])
            ->make(true);
    }

    public function canteen(Request $request)
    {
        // $checkExist = Canteen::select("*")->where('created_at', '>=', Carbon::now()->subHours(3)->toDateTimeString())->where('npk', '=', $request->npk)->get();
        // // dd(Carbon::now()->subHours(3)->toDateTimeString());
        // // dd($checkExist);
        // // $checkExist = Canteen::select("*")->where('date', '=', $this->dateNow->toDateString())->where('npk', '=', $request->npk)->get();
        // $this->validate($request, [
        //     'npk' => 'required',
        //     'name' => 'required',
        //     'date' => 'required',
        //     'canteen_no' => 'required',
        // ]);

        // if (count($checkExist) == 0) {
        //     Canteen::create([
        //         'canteen_no' => $request->canteen_no,
        //         'npk' => $request->npk,
        //         'name' => $request->name,
        //         'date' => $request->date,
        //     ]);
        //     Alert::success('Scan Successfully!', 'Employee ' . $request->npk . ' - ' . $request->name . ' successfully scanned!')->autoClose(3000);
        // } else {
        //     Alert::error('Alert!', 'Employee ' . $request->npk . ' - ' . $request->name . ' already scanned!')->autoClose(3000);
        // }

        // if ($request->canteen_no == 1) {
        //     return redirect('/canteen/scancanteen1');
        // } else {
        //     return redirect('/canteen/scancanteen2');
        // }

        // return Redirect::back();
    }

    public function export_excel(Request $request)
    {
        // dd($request->fromdate);
        $username = Auth::user()->name;
        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());
        $ipAddress = request()->ip();
        $macAddress = get_mac_address($ipAddress);
        $browser = $agent->browser();
        $os = $agent->platform();
        Syslog::create([
            'username' => $username,
            'activity' => 'export excel from ' . $request->fromdate . ' to ' . $request->todate . ' on canteen ' . $request->canteen_no,
            'menu' => 'Canteen',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'browser_type' => $browser,
            'os' => $os,
        ]);

        return Excel::download(new CanteensExport($request->fromdate, $request->todate, $request->canteen_no, $request->break), 'Canteen Data_' . $request->fromdate . '_' . $request->todate . '_Kantin ' . $request->canteen_no . '_' . $request->break . '.xlsx');
    }

    // public function synchronize(Request $request)
    // {
    //     $canteens = Canteen::where('canteen_no', $request->canteen_no)->get();
    //     foreach ($canteens as $canteen) {
    //         DB::connection('sqlsrvcanteen')->table('canteen')->updateOrInsert([
    //         'canteen_no' => $canteen->canteen_no,
    //         'npk' => $canteen->npk,
    //         'name' => $canteen->name,
    //         'dept' => $canteen->dept,
    //         'date' => $canteen->date,
    //         'created_at' => $canteen->created_at,
    //         'updated_at' => $canteen->updated_at,
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Canteen synchronized successfully',
    //     ]);
    // }
}
