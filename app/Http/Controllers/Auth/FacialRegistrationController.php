<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class FacialRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.facial-register');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'facial_descriptors' => 'required|string',
            'facial_image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'facial_descriptors' => $request->facial_descriptors,
            'facial_image' => $request->facial_image,
        ]);

        event(new Registered($user));

        // Log the user in
        auth()->login($user);

        return redirect()->route('dashboard');
    }
}
