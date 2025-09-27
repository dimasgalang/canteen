<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use App\Models\CanteenTwo;
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
    protected $now;
    protected $dateNow;

    public function __construct()
    {
        $this->now = Carbon::now();
        $this->dateNow = Carbon::today();
    }

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
    //     $checkExist = Canteen::select("*")->where('created_at', '<=', $this->now->subHours(0)->toDateTimeString())->where('npk', '=', $request->npk)->get();
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
            // $decrypted = Crypt::decryptString($request->barcode);
            $exploding = explode('_', $request->barcode);
            $npk = $exploding[0];
            $name = $exploding[1];
            if (!empty($exploding[2])) {
                $dept = $exploding[2];
            } else {
                $dept = null;
            }
            // dd($name);

            $checkExistFirst = Canteen::select("*")->where('created_at', '>=', $this->dateNow->setTime(11, 30, 0))->where('created_at', '<', $this->dateNow->setTime(14, 0, 0))->where('npk', '=', $npk)->get();
            $checkExistSecond = CanteenTwo::select("*")->where('created_at', '>=', $this->dateNow->setTime(11, 30, 0))->where('created_at', '<', $this->dateNow->setTime(14, 0, 0))->where('npk', '=', $npk)->get();

            $checkLemburExistFirst = Canteen::select("*")->where('created_at', '>=', $this->dateNow->setTime(16, 30, 0))->where('created_at', '<', $this->dateNow->setTime(18, 0, 0))->where('npk', '=', $npk)->get();
            $checkLemburExistSecond = CanteenTwo::select("*")->where('created_at', '>=', $this->dateNow->setTime(16, 30, 0))->where('created_at', '<', $this->dateNow->setTime(18, 0, 0))->where('npk', '=', $npk)->get();

            $checkEmployee = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*')->where('BIODATA.NPK', '=', $npk)->get();
            // dd($checkEmployee);

            // dd($checkLemburExist);
            if (count($checkEmployee) > 0) {
                if (count($checkExistFirst) < 1 && count($checkExistSecond) < 1 && $this->now >= $this->dateNow->setTime(11, 30, 0) && $this->now < $this->dateNow->setTime(14, 00, 0)) {
                    Canteen::firstOrCreate([
                        'canteen_no' => 1,
                        'npk' => $npk,
                        'name' => $name,
                        'dept' => $dept,
                        'date' => $this->now
                    ]);
                    Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                } else {
                    if ($this->now < $this->dateNow->setTime(14, 0, 0)) {
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned!')->autoClose(500);
                    } elseif (($this->now < $this->dateNow->setTime(16, 30, 0)) && ($this->now > $this->dateNow->setTime(14, 0, 0))) {
                        Alert::warning('Alert!', 'Belum masuk waktu istirahat ke-2!')->autoClose(2500);
                    } elseif (($this->now >= $this->dateNow->setTime(16, 30, 0)) && ($this->now <= $this->dateNow->setTime(18, 00, 0)) && (count($checkExistFirst) >= 0) && (count($checkExistSecond) >= 0) && (count($checkLemburExistFirst) < 1) && (count($checkLemburExistSecond) < 1)) {
                        Canteen::firstOrCreate([
                            'canteen_no' => 1,
                            'npk' => $npk,
                            'name' => $name,
                            'dept' => $dept,
                            'date' => $this->now
                        ]);
                        Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                    } else {
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned in other canteen!')->autoClose(500);
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
            // $decrypted = Crypt::decryptString($request->barcode);
            // dd("Subhours 0 = " . $this->now->subHours(0)->toDateTimeString() . " Subhours 4 = " . $this->now->subHours(4)->toDateTimeString() . " Carbon = " . $this->dateNow->setTime(18, 0, 0));
            $exploding = explode('_', $request->barcode);
            $npk = $exploding[0];
            $name = $exploding[1];
            if (!empty($exploding[2])) {
                $dept = $exploding[2];
            } else {
                $dept = null;
            }

            $checkExistFirst = Canteen::select("*")->where('created_at', '>=', $this->dateNow->setTime(11, 30, 0))->where('created_at', '<', $this->dateNow->setTime(14, 0, 0))->where('npk', '=', $npk)->get();
            $checkExistSecond = CanteenTwo::select("*")->where('created_at', '>=', $this->dateNow->setTime(11, 30, 0))->where('created_at', '<', $this->dateNow->setTime(14, 0, 0))->where('npk', '=', $npk)->get();

            $checkLemburExistFirst = Canteen::select("*")->where('created_at', '>=', $this->dateNow->setTime(16, 30, 0))->where('created_at', '<', $this->dateNow->setTime(18, 0, 0))->where('npk', '=', $npk)->get();
            $checkLemburExistSecond = CanteenTwo::select("*")->where('created_at', '>=', $this->dateNow->setTime(16, 30, 0))->where('created_at', '<', $this->dateNow->setTime(18, 0, 0))->where('npk', '=', $npk)->get();

            $checkEmployee = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*')->where('BIODATA.NPK', '=', $npk)->get();

            // dd($checkLemburExist);
            if (count($checkEmployee) > 0) {
                if (count($checkExistFirst) < 1 && count($checkExistSecond) < 1 && $this->now >= $this->dateNow->setTime(11, 30, 0) && $this->now < $this->dateNow->setTime(14, 00, 0)) {
                    CanteenTwo::firstOrCreate([
                        'canteen_no' => 2,
                        'npk' => $npk,
                        'name' => $name,
                        'dept' => $dept,
                        'date' => $this->now
                    ]);
                    Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                } else {
                    if ($this->now < $this->dateNow->setTime(14, 0, 0)) {
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned!')->autoClose(500);
                    } elseif (($this->now < $this->dateNow->setTime(16, 30, 0)) && ($this->now > $this->dateNow->setTime(14, 0, 0))) {
                        Alert::warning('Alert!', 'Belum masuk waktu istirahat ke-2!')->autoClose(2500);
                    } elseif (($this->now >= $this->dateNow->setTime(16, 30, 0)) && ($this->now <= $this->dateNow->setTime(18, 00, 0)) && (count($checkExistFirst) >= 0) && (count($checkExistSecond) >= 0) && (count($checkLemburExistFirst) < 1) && (count($checkLemburExistSecond) < 1)) {
                        CanteenTwo::firstOrCreate([
                            'canteen_no' => 2,
                            'npk' => $npk,
                            'name' => $name,
                            'dept' => $dept,
                            'date' => $this->now
                        ]);
                        Alert::success('Scan Successfully!', 'Employee ' . $npk . ' - ' . $name . ' successfully scanned!')->autoClose(500);
                    } else {
                        Alert::error('Alert!', 'Employee ' . $npk . ' - ' . $name . ' already scanned in other canteen!')->autoClose(500);
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
