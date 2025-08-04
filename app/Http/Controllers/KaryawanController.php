<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\QRFiles;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;
use Illuminate\Support\Facades\Crypt;
use AgeekDev\Barcode\Facades\Barcode;
use AgeekDev\Barcode\Enums\BarcodeType;
use App\Models\BarcodeFiles;

class KaryawanController extends Controller
{
    public function index()
    {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->orderBy('DEPARTEMENT', 'ASC')->get();
        return view('karyawan.index', compact('employees'));
        // return view('karyawan.index');
    }

    public function show($id)
    {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('PKWT.*', 'DEPT.DEPARTEMENT', 'BIODATA.ID_DEPT')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->leftJoin('PKWT', 'BIODATA.NPK', '=', 'PKWT.NPK')->where('BIODATA.NPK', '=', $id)->get();
        return response()->json($employees);
    }


    public function batchQR()
    {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->orderBy('DEPARTEMENT', 'ASC')->get();

        foreach ($employees as $employee) {
            $path = storage_path('public/qr/');
            // $qr_data = $employee->NPK;
            // $qr_data = "65824_DIMAS GALANG RAMADHAN_IT";
            // $encrypted = Crypt::encryptString($qr_data);
            // $qr_data = "canteen?npk=" . $employee->NPK . "&name=" . $employee->NAMA_KARYAWAN . "&dept=" . $employee->DEPARTEMENT;
            $qr_data = $employee->NPK . "_" . $employee->NAMA_KARYAWAN;
            $qr = FacadesQrCode::format('png')->generate($qr_data);

            // $qrImageName = '65824-DIMAS GALANG RAMADHAN-IT.png';
            $qrImageName = $employee->NPK . "_" . $employee->NAMA_KARYAWAN . '.png';

            Storage::put('public/qr/' . $qrImageName, $qr);

            QRFiles::firstOrCreate([
                'npk' => $employee->NPK,
                'qr_data' => $qr_data,
                'qr_name' => $qrImageName,
                'qr_path' => $path
            ]);

            // QRFiles::firstOrCreate([
            //     'npk' => "65824",
            //     'qr_data' => $qr_data,
            //     'qr_name' => $qrImageName,
            //     'qr_path' => $path
            // ]);
        }
        Alert::success('Batch Successfully!', 'QR Code successfully generated!');
        return redirect('/karyawan/index');
    }

    public function batchBarcode()
    {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->orderBy('DEPARTEMENT', 'ASC')->get();

        foreach ($employees as $employee) {
            $path = storage_path('public/barcode/');
            // $qr_data = $employee->NPK;
            // $barcode_data = "65824_DIMAS GALANG RAMADHAN_IT";
            // $encrypted = md5($barcode_data);
            $barcode_data = $employee->NPK . "_" . $employee->NAMA_KARYAWAN . "_" . $employee->DEPARTEMENT;
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode = Barcode::imageType("png")
                ->foregroundColor("#000000")
                ->height(100)
                ->widthFactor(1)
                ->type(BarcodeType::CODE_128)
                ->generate($barcode_data);
            // $barcode = $generator->getBarcode($encrypted, $generator::TYPE_CODE_128);
            $barcodeImageName = $employee->NPK . "_" . $employee->NAMA_KARYAWAN . "_" . $employee->DEPARTEMENT . '.png';
            // $qrImageName = $employee->NPK . "-" . $employee->NAMA_KARYAWAN . '.png';

            Storage::put('public/barcode/' . $barcodeImageName, $barcode);

            BarcodeFiles::firstOrCreate([
                'npk' => $employee->NPK,
                'barcode_data' => $barcode_data,
                'barcode_name' => $barcodeImageName,
                'barcode_path' => $path
            ]);

            // BarcodeFiles::firstOrCreate([
            //     'npk' => "65824",
            //     'barcode_data' => $barcode_data,
            //     'barcode_name' => $barcodeImageName,
            //     'barcode_path' => $path
            // ]);
        }
        Alert::success('Batch Successfully!', 'Barcode successfully generated!');
        return redirect('/karyawan/index');
    }

    public function generateqr($id)
    {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('PKWT.*', 'DEPT.DEPARTEMENT', 'BIODATA.*')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->leftJoin('PKWT', 'BIODATA.NPK', '=', 'PKWT.NPK')->where('BIODATA.NPK', '=', $id)->get();

        // $path = storage_path('public/qr/single/');
        $path = storage_path('public/qr/');
        // $qr_data = $employee->NPK;
        $qr_data = $employees[0]->NPK . "_" . $employees[0]->NAMA_KARYAWAN;
        $qr = FacadesQrCode::format('png')->generate($qr_data);
        $qrImageName = $employees[0]->NPK . "_" . $employees[0]->NAMA_KARYAWAN . '.png';

        // Storage::put('public/qr/single/' . $qrImageName, $qr);
        Storage::put('public/qr/' . $qrImageName, $qr);

        QRFiles::firstOrCreate([
            'npk' => $employees[0]->NPK,
            'qr_data' => $qr_data,
            'qr_name' => $qrImageName,
            'qr_path' => $path
        ]);
        Alert::success('Generate QR Successfully!', 'QR Code successfully generated!');
        return redirect('/karyawan/index');
    }
}
