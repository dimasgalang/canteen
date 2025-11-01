<?php

namespace App\Http\Controllers;

use App\Models\Syslog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $username = User::where('email', $request->email)->value('name');
        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());
        $ipAddress = $request->ip();
        $mac = get_mac_address($ipAddress);
        $browser = $agent->browser();
        $os = $agent->platform();
        
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // untuk membuat generic unique id per user login attempt
        $key = $this->throttleKey($request);

        // cek apakah user sudah kena banned/restrict percobaan login
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            SysLog::create([
                'username' => $username,
                'activity' => 'Login : Failed - Too Many Attempts',
                'menu' => 'Login',
                'log_date' => now(),
                'ip_address' => $ipAddress,
                'mac_address' => $mac,
                'browser_type' => $browser,
                'os' => $os,
            ]);
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            RateLimiter::clear($key);

            // Simpan log aktivitas user
            SysLog::create([
                'username' => $username,
                'activity' => 'Login : Success',
                'menu' => 'Login',
                'log_date' => now(),
                'ip_address' => $ipAddress,
                'mac_address' => $mac,
                'browser_type' => $browser,
                'os' => $os,
            ]);

            Alert::success('Login Successfully!', 'Welcome To Chutex E-Canteen Sistem');
            return redirect()->intended('/home');
        }

        RateLimiter::hit($key, 600);
        
        SysLog::create([
            'username' => $username,
            'activity' => 'Login : Failed - Invalid Credentials',
            'menu' => 'Login',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $mac,
            'browser_type' => $browser,
            'os' => $os,
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('email')).'|'.$request->ip();
    }

    public function logout()
    {
        $username = Auth::user()->name;
        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());
        $ipAddress = request()->ip();
        $macAddress = get_mac_address($ipAddress);
        $browser = $agent->browser();
        $os = $agent->platform();
        Syslog::create([
            'username' => $username,
            'activity' => 'Logout by user ' . $username,
            'menu' => 'Logout',
            'log_date' => now(),
            'ip_address' => $ipAddress,
            'mac_address' => $macAddress,
            'browser_type' => $browser,
            'os' => $os,
        ]);
        
        Auth::logout();
        Alert::success('Logout Successfully!', 'See You Next Time');
        return redirect('/login');
    }
}
