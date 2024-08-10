<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\PermissionCollection;



class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['expiration'] = 30 * 24 * 60;
            $success['name'] =  $authUser->name;
            $success['type'] =  $authUser->type;
            $success['photoUrl'] =  $authUser->photoUrl;

            if ($authUser->isNormal) {
                $success['permissions'] = new PermissionCollection($authUser->permissions);
            }

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 403);
        }
    }
}
