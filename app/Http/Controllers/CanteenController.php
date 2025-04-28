<?php

namespace App\Http\Controllers;

use App\Exports\CanteensExport;
use App\Models\Canteen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class CanteenController extends Controller
{
    public function index()
    {
        $canteens = Canteen::select('*')->where('date', '=', Carbon::today()->toDateString())->orderByDesc('created_at')->get();
        return view('canteen.index', compact('canteens'));
    }

    public function scancanteen1()
    {
        $canteens = Canteen::select('*')->where('date', '=', Carbon::today()->toDateString())->where('canteen_no', '=', '1')->orderByDesc('created_at')->get();
        // dd(Carbon::today()->toDateString());
        return view('canteen.canteen1', compact('canteens'));
    }

    public function scancanteen2()
    {
        $canteens = Canteen::select('*')->where('date', '>', Carbon::today()->toDateString())->where('canteen_no', '=', '2')->orderByDesc('created_at')->get();
        return view('canteen.canteen2', compact('canteens'));
    }

    public function showcanteen(Request $request)
    {
        if ($request->ajax()) {
            $canteens = Canteen::orderBy('created_at', 'desc');
            return DataTables::of($canteens)
                ->addIndexColumn()
                ->addColumn('created_at_formated', function ($row) {
                    return date('d-m-Y h:i:s', strtotime($row->created_at));
                })
                ->rawColumns(['created_at_formated'])
                ->filter(function ($instance) use ($request) {
                    if ($request->filled('fromdate') && $request->filled('todate')) {
                        $instance
                            ->where('date', '>=', $request->get('fromdate'))
                            ->where('date', '<=', $request->get('todate'))
                            ->where('canteen_no', '=', $request->get('canteen_no'));
                    }
                })->make(true);
        };
    }


    public function showcanteen1()
    {
        $canteens = Canteen::orderBy('created_at', 'desc')->where('date', '=', Carbon::today()->toDateString())->where('canteen_no', '=', '1')->get();
        return DataTables::of($canteens)
            ->addIndexColumn()
            ->addColumn('created_at_formated', function ($row) {
                return date('d-m-Y h:i:s', strtotime($row->created_at));
            })
            ->rawColumns(['created_at_formated'])
            ->make(true);
    }


    public function showcanteen2()
    {
        $canteens = Canteen::orderBy('created_at', 'desc')->where('date', '=', Carbon::today()->toDateString())->where('canteen_no', '=', '2')->get();
        return DataTables::of($canteens)
            ->addIndexColumn()
            ->addColumn('created_at_formated', function ($row) {
                return date('d-m-Y h:i:s', strtotime($row->created_at));
            })
            ->rawColumns(['created_at_formated'])
            ->make(true);
    }

    public function canteen(Request $request)
    {
        $checkExist = Canteen::select("*")->where('created_at', '>=', Carbon::now()->subHours(3)->toDateTimeString())->where('npk', '=', $request->npk)->get();
        // dd(Carbon::now()->subHours(3)->toDateTimeString());
        // dd($checkExist);
        // $checkExist = Canteen::select("*")->where('date', '=', Carbon::today()->toDateString())->where('npk', '=', $request->npk)->get();
        $this->validate($request, [
            'npk' => 'required',
            'name' => 'required',
            'date' => 'required',
            'canteen_no' => 'required',
        ]);

        if (count($checkExist) == 0) {
            Canteen::create([
                'canteen_no' => $request->canteen_no,
                'npk' => $request->npk,
                'name' => $request->name,
                'date' => $request->date,
            ]);
            Alert::success('Scan Successfully!', 'Employee ' . $request->npk . ' - ' . $request->name . ' successfully scanned!')->autoClose(3000);
        } else {
            Alert::error('Alert!', 'Employee ' . $request->npk . ' - ' . $request->name . ' already scanned!')->autoClose(3000);
        }

        if ($request->canteen_no == 1) {
            return redirect('/canteen/scancanteen1');
        } else {
            return redirect('/canteen/scancanteen2');
        }

        // return Redirect::back();
    }

    public function export_excel(Request $request)
    {
        // dd($request->fromdate);
        return Excel::download(new CanteensExport($request->fromdate, $request->todate, $request->canteen_no), 'Canteen Data_' . $request->fromdate . '_' . $request->todate . '_Kantin ' . $request->canteen_no . '.xlsx');
    }
}
