<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacialLoginController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function showLoginForm()
    {
        return view('auth.facial-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'facial_descriptors' => 'required|string',
        ]);

        $loginDescriptors = json_decode($request->facial_descriptors, true);
        
        // Get all users with facial descriptors
        $users = $this->userService?->fetchFacialUsers();
        
        $matchedUser = null;
        $highestSimilarity = 0;
        $threshold = config('voting.face_threshold');

        foreach ($users as $user) {
            $storedDescriptors = json_decode($user->facial_descriptors, true);
            
            if ($storedDescriptors) {
                $similarity = $this->calculateSimilarity($loginDescriptors, $storedDescriptors);
                
                if ($similarity > $highestSimilarity && $similarity > $threshold) {
                    $highestSimilarity = $similarity;
                    $matchedUser = $user;
                }
            }
        }

        if ($matchedUser) {
            Auth::login($matchedUser);
            
            // Log the facial login
            activity('auth')
                ->causedBy($matchedUser)
                ->log('Logged in using facial recognition');
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => route('dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Face not recognized. Please try again or use email login.'
        ], 401);
    }

    private function calculateSimilarity($descriptors1, $descriptors2)
    {
        if (count($descriptors1) !== count($descriptors2)) {
            return 0;
        }

        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;

        for ($i = 0; $i < count($descriptors1); $i++) {
            $dotProduct += $descriptors1[$i] * $descriptors2[$i];
            $norm1 += pow($descriptors1[$i], 2);
            $norm2 += pow($descriptors2[$i], 2);
        }

        $norm1 = sqrt($norm1);
        $norm2 = sqrt($norm2);

        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }

        return $dotProduct / ($norm1 * $norm2);
    }
}
