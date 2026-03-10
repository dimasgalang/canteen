<?php

namespace App\Http\Controllers;

use App\Models\Outsource;
use App\Models\Syslog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use RealRashid\SweetAlert\Facades\Alert;

class OutsourceController extends Controller
{
    public function index()
    {
        $outsources = Outsource::where('void', 'false')->orWhereNull('void')->get();
        return view('outsource.index', compact('outsources'));
    }

    public function create()
    {
        return view('outsource.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'NPK' => 'required',
            'NAMA' => 'required',
            'VENDOR' => 'required',
        ]);

        Outsource::create([
            'NPK' => $request->NPK,
            'NAMA' => $request->NAMA,
            'VENDOR' => $request->VENDOR,
        ]);

        $username = Auth::user()->name;
        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());
        $ipAddress = request()->ip();
        $macAddress = get_mac_address($ipAddress);
        $browser = $agent->browser();
        $os = $agent->platform();
        Syslog::create([
            'username' => $username,
            'activity' => 'Create New Outsource ' . $request->NAMA,
            'menu' => 'Outsource',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'browser_type' => $browser,
            'os' => $os,
        ]);

        Alert::success('Create Successfully!', 'Outsource successfully created!');
        return redirect()->route('outsource.index');
    }

    public function getNpk(Request $request)
    {
        $outsource = Outsource::where('NAMA', $request->name)->where(function ($query) {
            $query->where('void', 'false')->orWhereNull('void');
        })->first();

        if ($outsource) {
            return response()->json(['npk' => $outsource->NPK]);
        }

        return response()->json(['npk' => '']);
    }

    public function edit($id)
    {
        $outsource = Outsource::findOrFail($id);
        return view('outsource.edit', compact('outsource'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NPK' => 'required',
            'NAMA' => 'required',
            'VENDOR' => 'required',
        ]);

        $outsource = Outsource::findOrFail($id);

        $outsource->update([
            'NPK' => $request->NPK,
            'NAMA' => $request->NAMA,
            'VENDOR' => $request->VENDOR,
        ]);

        $username = Auth::user()->name;
        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());
        $ipAddress = request()->ip();
        $macAddress = get_mac_address($ipAddress);
        $browser = $agent->browser();
        $os = $agent->platform();
        Syslog::create([
            'username' => $username,
            'activity' => 'Update Outsource ' . $request->NAMA,
            'menu' => 'Outsource',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'browser_type' => $browser,
            'os' => $os,
        ]);

        Alert::success('Update Successfully!', 'Outsource successfully updated!');
        return redirect()->route('outsource.index');
    }

    public function delete($id)
    {
        $outsource = Outsource::findOrFail($id);
        $name = $outsource->NAMA;
        $outsource->update(['void' => 'true']);

        $username = Auth::user()->name;
        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());
        $ipAddress = request()->ip();
        $macAddress = get_mac_address($ipAddress);
        $browser = $agent->browser();
        $os = $agent->platform();
        Syslog::create([
            'username' => $username,
            'activity' => 'Delete Outsource ' . $name,
            'menu' => 'Outsource',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'browser_type' => $browser,
            'os' => $os,
        ]);

        Alert::success('Delete Successfully!', 'Outsource successfully deleted!');
        return redirect()->route('outsource.index');
    }
}
