<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\QRFiles;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use SimpleSoftwareIO\QrCode\Facades\QrCode as FacadesQrCode;

class KaryawanController extends Controller
{
    public function index() {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->orderBy('DEPARTEMENT', 'ASC')->get();
        return view('karyawan.index', compact('employees'));
    }

    public function show($id) {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('PKWT.*', 'DEPT.DEPARTEMENT', 'BIODATA.ID_DEPT')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->leftJoin('PKWT', 'BIODATA.NPK', '=', 'PKWT.NPK')->where('BIODATA.NPK', '=', $id)->get();
        return response()->json($employees);
    }
    

    public function batch()
    {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'DEPT.ID_DEPT', '=', 'BIODATA.ID_DEPT')->orderBy('DEPARTEMENT', 'ASC')->get();

        foreach ($employees as $employee) {
            $path = storage_path('public/qr/');
            // $qr_data = $employee->NPK;
            $qr_data = "canteen?npk=" . $employee->NPK . "&name=" . $employee->NAMA_KARYAWAN;
            $qr = FacadesQrCode::format('png')->generate($qr_data);
            $qrImageName = $employee->NPK . "-" . $employee->NAMA_KARYAWAN . '.png';

            Storage::put('public/qr/' . $qrImageName, $qr);

            QRFiles::firstOrCreate([
                'npk' => $employee->NPK,
                'qr_data' => $qr_data,
                'qr_name' => $qrImageName,
                'qr_path' => $path
            ]);
        }
        Alert::success('Batch Successfully!', 'QR Code successfully generated!');
        return redirect('/karyawan/index');
    }
}
