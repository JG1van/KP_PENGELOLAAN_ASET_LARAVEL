<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('pengaturan.profil', compact('user'));
    }

    public function updateAll(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|required_with:current_password|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama salah.'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        $message = $request->filled('new_password')
            ? 'Profil dan password berhasil diperbarui.'
            : 'Profil berhasil diperbarui.';

        return redirect()->back()->with('success', $message);
    }

    public function destroy()
    {
        $user = Auth::user();

        // Perubahan langsung pada properti, bukan mass assignment
        $user->status = 'Tidak Aktif';
        $user->save();

        Auth::logout();

        return redirect()->route('login')->with('success', 'Akun Anda telah dinonaktifkan.');
    }
}
