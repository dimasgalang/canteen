<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Syslog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use RealRashid\SweetAlert\Facades\Alert;

class RoleController extends Controller
{
    public function index() {
        $roles = Role::all();
         return view('role.index', compact('roles'));
    }

    public function create() {
        return view('role.create');
    }

    public function store(Request $request)
    {
        Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
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
            'activity' => 'Create New Role ' . $request->name,
            'menu' => 'Role',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'browser_type' => $browser,
            'os' => $os,
        ]);

        Alert::success('Create Successfully!', 'Role successfully created!');
        return redirect()
            ->route('role.create');
    }

    public function delete($id) {
        $roles = Role::find($id);    
        $roles->delete();

        $username = Auth::user()->name;
        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());
        $ipAddress = request()->ip();
        $macAddress = get_mac_address($ipAddress);
        $browser = $agent->browser();
        $os = $agent->platform();
        SysLog::create([
            'username' => $username,
            'activity' => 'Delete Role ' . $roles->name,
            'menu' => 'Role',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'browser_type' => $browser,
            'os' => $os,
        ]);

        Alert::success('Delete Successfully!', 'Role successfully deleted!');
        return redirect('role/index');
    }
}
