<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\LoginRequest;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{

    public function __invoke(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return api_response('username atau password tidak valid.', 401);
        }
    
        return api_response($user->createToken('token')->plainTextToken);
    }
}
