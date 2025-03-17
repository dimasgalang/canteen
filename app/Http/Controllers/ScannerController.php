<?php

namespace App\Http\Controllers;

use App\Models\Canteen;
use App\Models\QRCode;
use App\Models\QRFiles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;

class ScannerController extends Controller
{
    public function qrscanning()
    {
        return view('scanner.qrscanning2');
    }

    public function scanning()
    {
        return view('scanner.scanning');
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


    public function canteen(Request $request)
    {
        $checkExist = Canteen::select("*")->where('created_at', '<=', Carbon::now()->subHours(0)->toDateTimeString())->where('npk', '=', $request->npk)->get();
        $this->validate($request, [
            'npk' => 'required',
            'name' => 'required',
            'canteen_no' => 'required',
        ]);

        if (count($checkExist) < 1) {
            Canteen::firstOrCreate([
                'canteen_no' => $request->canteen_no,
                'npk' => $request->npk,
                'name' => $request->name,
            ]);
        }

        Alert::success('Scan Successfully!', 'Employee ' . $request->npk . ' - ' . $request->name . ' successfully scanned!');
        return redirect('/scanner/qrscanning');
    }

    public function barcode(Request $request)
    {

        $checkExist = Canteen::select("*")->where('created_at', '<=', Carbon::now()->subHours(0)->toDateTimeString())->where('npk', '=', $request->npk)->get();
        // dd($checkExist);
        dd(count($checkExist));
        $this->validate($request, [
            'npk' => 'required',
            'canteen_no' => 'required',
        ]);

        if (count($checkExist) < 1) {
            Canteen::firstOrCreate([
                'canteen_no' => $request->canteen_no,
                'npk' => $request->npk,
            ]);
        }

        Alert::success('Scan Successfully!', 'Employee ' . $request->npk . ' successfully scanned!');
        return redirect('/scanner/scanning');
    }
}
