<?php

namespace App\Http\Controllers;

use App\Models\ModelHasRoles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index() {
        $users   = User::all();
        return view('user.index', compact('users'));
    }

    public function profile()
    {
        $user = User::select('users.*','signatures.signature_img')
        ->leftJoin('signatures','users.id','=','signatures.user_id')
        ->where('users.id','=', Auth::user()->id)
        ->get();
        return view('user.profile', compact('user'));
    }

    public function delete($id) {
        $users = User::find($id);    
        $users->delete();
        Alert::success('Delete Successfully!', 'User ' . $users->name . ' successfully deleted!');
        return redirect()->intended('user/index');
    }

    public function assign($id) {
        $modelhasroles = User::select('users.name','users.email','users.id','model_has_roles.*')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->where('users.id', '=', $id)
        ->get();
        $roles = Role::all();
        return view('auth.assign', compact('modelhasroles','roles'));
    }

    public function assignrole(Request $request)
    {
        $modelhasroles = ModelHasRoles::where('model_id', '=', $request->id)
        ->where('model_type', '=', 'App\Models\User')
        ->delete();
        ModelHasRoles::updateOrCreate(
        [
            'role_id' => $request->role_id,
            'model_type' => 'App\Models\User',
            'model_id' => $request->id
            ]
        );

        Alert::success('Assign Successfully!', 'User successfully assigned!');
        return redirect()->intended('user/index');
    }
}
