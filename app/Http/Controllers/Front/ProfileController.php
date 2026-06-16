<?php

namespace App\Http\Controllers\Front;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile_update(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            //'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            //'password' => ['required', 'string', 'min:8', 'confirmed'],
            //'password_confirmation' => ['required'],
        ]);
        $user = User::findOrFail(auth()->user()->id);
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->name = $request->name;
        $user->title = $request->title;
        $user->phone = $request->phone;
        $user->update();
        return redirect()->back()->with('message', 'Profile Updated');
    }

    public function change_password(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        if($request->isMethod('POST')) {
            if(Hash::check($request->old_password, auth()->user()->getAuthPassword())) {
                $request->validate([
                    'old_password' => ['required', 'string'],
                    'password' => ['required', 'string', 'min:6', 'confirmed'],
                    'password_confirmation' => ['required'],
                ]);
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect()->route('password.change')->with('message', 'Password changed successfully.');
            } else {
                return back()->withErrors(['old_password' => 'Current password did not match.']);
            }
        }

        return view('auth.passwords.change');
    }

}
