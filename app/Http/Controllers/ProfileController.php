<?php

// app/Http/Controllers/ProfileController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{    
    public function changePhoto()
    {
        $breadcrumb = (object) [
            'title' => 'Profile',
            'list'  => ['Home', 'Profile']
        ];

        $page = (object) [  
            'title' => 'Profile'
        ];

        $activeMenu = 'profile';
        
        return view('profile.change-photo', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        // Validasi file input
        $validator = \Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
    
        if ($validator->fails()) {
            // Kirim response JSON error dengan pesan custom
            return redirect()->back()->with('error', 'File yang diunggah tidak valid!');
        }

        if ($request->hasFile('profile_picture')) {

            // Jika sebelumnya ada foto dan file-nya masih ada, hapus dulu
            if ($user->profile_picture && file_exists(public_path('storage/profile/' . $user->profile_picture))) {
                unlink(public_path('storage/profile/' . $user->profile_picture));
            }

            // Simpan foto baru
            $photo = $request->file('profile_picture');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('storage/profile'), $filename);

            // Update nama file di database
            $user->profile_picture = $filename;
        }

        // Simpan perubahan user
        $user->save();

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}