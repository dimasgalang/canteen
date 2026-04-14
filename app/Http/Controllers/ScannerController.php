<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use App\Models\CanteenTwo;
use App\Models\DuplicateScanner;
use App\Models\QRCode;
use App\Models\QRFiles;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;
use Illuminate\Support\Facades\Crypt;

class ScannerController extends Controller
{
    // protected $now;
    // protected $dateNow;

    // public function __construct()
    // {
    //     $this->now = Carbon::now();
    //     $this->dateNow = Carbon::today();
    // }

    public function checkscanning()
    {
        return view('scanner.checkscanning');
    }

    public function barcodescanning1()
    {
        return view('scanner.barcodescanning1');
    }

    public function barcodescanning2()
    {
        return view('scanner.barcodescanning2');
    }

    public function create()
    {
        return view('scanner.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'npk' => 'required',
            'name' => 'required',
            'canteen_no' => 'required',
        ]);

        $path = storage_path('public/qr/');
        $qr_data = "canteen?npk=" . $request->npk . "&canteen_no=" . $request->canteen_no;
        $qr = FacadesQrCode::format('png')->generate($qr_data);
        $qrImageName = $request->npk . "-" . $request->canteen_no . '.png';
        // dd($qrImageName);

        Storage::put('public/qr/' . $qrImageName, $qr);

        QRFiles::firstOrCreate([
            'npk' => $request->npk,
            'name' => $request->name,
            'canteen_no' => $request->canteen_no,
            'qr_data' => $qr_data,
            'qr_name' => $qrImageName,
            'qr_path' => $path
        ]);

        Alert::success('Create Successfully!', 'QR Code ' . $request->npk . ', Kantin ' . $request->canteen_no . ' successfully created!');
        return redirect('/scanner/create');
    }


    // public function canteen(Request $request)
    // {
    //     $checkExist = Canteen::select("*")->where('created_at', '<=', Carbon::now()->subHours(0)->toDateTimeString())->where('npk', '=', $request->npk)->get();
    //     $this->validate($request, [
    //         'npk' => 'required',
    //         'name' => 'required',
    //         'canteen_no' => 'required',
    //     ]);

    //     if (count($checkExist) < 1) {
    //         Canteen::firstOrCreate([
    //             'canteen_no' => $request->canteen_no,
    //             'npk' => $request->npk,
    //             'name' => $request->name,
    //         ]);
    //     }

    //     Alert::success('Scan Successfully!', 'Employee ' . $request->npk . ' - ' . $request->name . ' successfully scanned!');
    //     return redirect('/scanner/qrscanning');
    // }

    public function barcodecanteen1(Request $request)
    {
        try {
            $exploding = explode('_', $request->barcode);
            $npk = $exploding[0];
            $name = $exploding[1];
            $dept = $exploding[2] ?? null;

            $now = Carbon::now();
            $today = Carbon::today();

            // Time ranges
            $lunchStart = $today->copy()->setTime(11, 30, 0);
            $lunchEnd = $today->copy()->setTime(14, 0, 0);
            $dinnerStart = $today->copy()->setTime(16, 30, 0);
            $dinnerEnd = $today->copy()->setTime(18, 0, 0);

            // Check Employee using raw query with NOLOCK for performance
            $checkEmployee = DB::connection('sqlsrv')->select("SELECT TOP 1 NPK FROM BIODATA WITH (NOLOCK) WHERE NPK = ?", [$npk]);

            if (count($checkEmployee) > 0) {
                // Check existing scans using exists() which is faster than get()
                $checkExistFirst = Canteen::whereBetween('created_at', [$lunchStart, $lunchEnd])->where('npk', $npk)->exists();
                $checkExistSecond = CanteenTwo::whereBetween('created_at', [$lunchStart, $lunchEnd])->where('npk', $npk)->exists();

                if (!$checkExistFirst && !$checkExistSecond && $now >= $lunchStart && $now < $lunchEnd) {
                    Canteen::create([
                        'canteen_no' => 1,
                        'npk' => $npk,
                        'name' => $name,
                        'dept' => $dept,
                        'date' => $now
                    ]);
                    Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                } else {
                    if ($now < $lunchStart) {
                        Alert::warning('Alert!', 'Belum masuk waktu istirahat ke-1!')->autoClose(500);
                    } elseif ($now > $lunchEnd && $now < $dinnerStart) {
                        Alert::warning('Alert!', 'Belum masuk waktu istirahat ke-2!')->autoClose(500);
                    } elseif ($now >= $dinnerStart && $now <= $dinnerEnd) {
                        
                        $checkLemburExistFirst = Canteen::whereBetween('created_at', [$dinnerStart, $dinnerEnd])->where('npk', $npk)->exists();
                        $checkLemburExistSecond = CanteenTwo::whereBetween('created_at', [$dinnerStart, $dinnerEnd])->where('npk', $npk)->exists();

                        if (!$checkLemburExistFirst && !$checkLemburExistSecond) {
                             Canteen::create([
                                'canteen_no' => 1,
                                'npk' => $npk,
                                'name' => $name,
                                'dept' => $dept,
                                'date' => $now
                            ]);
                            Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                        } else {
                             Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned for dinner!')->autoClose(500);
                        }
                       
                    } elseif ($checkExistSecond) {
                        DuplicateScanner::firstOrCreate([
                            'npk' => $npk,
                            'already_scan_canteen_number' => 2,
                            'date' => Carbon::today()
                        ], [
                            'name' => $name,
                            'need_to_scan_canteen_number' => 1,
                        ]);
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned in canteen 2!')->autoClose(500);
                    } else {
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned!')->autoClose(500);
                    }
                }
            } else {
                Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' has been resign!')->autoClose(1000);
            }
        } catch (Exception $e) {
            Alert::error('Error!', "Invalid input, please check the qr data!")->autoClose(1000);
        }
        return redirect('/scanner/barcodescanning1');
    }

    public function barcodecanteen2(Request $request)
    {
        try {
            $exploding = explode('_', $request->barcode);
            $npk = $exploding[0];
            $name = $exploding[1];
            $dept = $exploding[2] ?? null;

            $now = Carbon::now();
            $today = Carbon::today();

            // Time ranges
            $lunchStart = $today->copy()->setTime(11, 30, 0);
            $lunchEnd = $today->copy()->setTime(14, 0, 0);
            $dinnerStart = $today->copy()->setTime(16, 30, 0);
            $dinnerEnd = $today->copy()->setTime(18, 0, 0);

            // Check Employee using raw query with NOLOCK for performance
            $checkEmployee = DB::connection('sqlsrv')->select("SELECT TOP 1 NPK FROM BIODATA WITH (NOLOCK) WHERE NPK = ?", [$npk]);

            if (count($checkEmployee) > 0) {
                // Check existing scans using exists() which is faster than get()
                $checkExistFirst = Canteen::whereBetween('created_at', [$lunchStart, $lunchEnd])->where('npk', $npk)->exists();
                $checkExistSecond = CanteenTwo::whereBetween('created_at', [$lunchStart, $lunchEnd])->where('npk', $npk)->exists();

                if (!$checkExistFirst && !$checkExistSecond && $now >= $lunchStart && $now < $lunchEnd) {
                    CanteenTwo::create([
                        'canteen_no' => 2,
                        'npk' => $npk,
                        'name' => $name,
                        'dept' => $dept,
                        'date' => $now
                    ]);
                    Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                } else {
                    if ($now < $lunchStart) {
                        Alert::warning('Alert!', 'Belum masuk waktu istirahat ke-1!')->autoClose(500);
                    } elseif ($now > $lunchEnd && $now < $dinnerStart) {
                        Alert::warning('Alert!', 'Belum masuk waktu istirahat ke-2!')->autoClose(500);
                    } elseif ($now >= $dinnerStart && $now <= $dinnerEnd) {
                        
                        $checkLemburExistFirst = Canteen::whereBetween('created_at', [$dinnerStart, $dinnerEnd])->where('npk', $npk)->exists();
                        $checkLemburExistSecond = CanteenTwo::whereBetween('created_at', [$dinnerStart, $dinnerEnd])->where('npk', $npk)->exists();

                        if (!$checkLemburExistFirst && !$checkLemburExistSecond) {
                             CanteenTwo::create([
                                'canteen_no' => 2,
                                'npk' => $npk,
                                'name' => $name,
                                'dept' => $dept,
                                'date' => $now
                            ]);
                            Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                        } else {
                             Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned for dinner!')->autoClose(500);
                        }
                       
                    } elseif ($checkExistFirst) {
                        DuplicateScanner::firstOrCreate([
                            'npk' => $npk,
                            'already_scan_canteen_number' => 1,
                            'date' => Carbon::today()
                        ], [
                            'name' => $name,
                            'need_to_scan_canteen_number' => 2,
                        ]);
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned in canteen 1!')->autoClose(500);
                    } else {
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned!')->autoClose(500);
                    }
                }
            } else {
                Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' has been resign!')->autoClose(1000);
            }
        } catch (Exception $e) {
            Alert::error('Error!', "Invalid input, please check the qr data!")->autoClose(1000);
        }
        return redirect('/scanner/barcodescanning2');
    }
}
