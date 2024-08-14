<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('profile.profile', ['user' => Auth::user()]);
    }
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }


    public function update(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);
        return redirect()->route('profile-edit')->with('status', 'Profil Berhasil Di Update!');
    }

    public function changepassword()
    {
        return view('profile.changepassword', ['user' => Auth::user()]);
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        $user = User::find(auth()->user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password Sebelumnya Salah!']);
        }

        $user->fill([
            'password' => Hash::make($request->new_password)
        ])->save();

        return back()->with('status', 'Password berhasil Diubah!');
    }
}
