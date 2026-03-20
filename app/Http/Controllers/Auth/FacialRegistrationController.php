<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class FacialRegistrationController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create()
    {
        return view('auth.facial-register');
    }

    public function store(Request $request)
    {
        // to check if the person sending request is robot or not
        if($request->border_name || !empty($request->border_name)) {
            return redirect()->back()->with('error', 'Bot detected!');
        }

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

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'facial_descriptors' => $request->facial_descriptors,
            'facial_image' => $request->facial_image,
        ];

        $user = $this->userService?->createUser($userData);

        event(new Registered($user));

        // Log the user in
        // auth()->login($user);
        Auth::login($user);

        return redirect(route('voting.index', absolute: false));
        // return redirect()->route('dashboard');
    }
}
