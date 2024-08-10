<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;

class UserController extends BaseController
{

    public function index(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            "page" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $usersQ = User::whereStatus("1")->orderBy('created_at', 'desc');

        $users = new UserCollection($usersQ->paginate(20));

        $total = $usersQ->count();

        $response = [
            "total" => $total,
            "data" => $users
        ];

        return $this->sendResponse($response, __('messages.found'));

    }

    public function show(User $user): JsonResponse
    {
        if ($user->status === "0")
            return $this->sendResponse([], __('messages.not_found'));

        $userResource = new UserResource($user);

        return $this->sendResponse($userResource, __('messages.found'));

    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'username' => 'required|unique:users,username',
            "photo" => 'image|mimes:png,jpg|max:5000',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'permissions' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $permissions = explode(',', $request->get('permissions'));
        $validator = Validator::make(['permissions' => $permissions], [
            'permissions' => 'required|array',
            'permissions.*' => 'required|exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $create = $request->except('permissions');
        if (isset($create["photo"])) {
            $request->file('photo')->store('users');
            $create["photo"] = $request->file('photo')->hashName();
        }

        $create['password'] = Hash::make($create['password']);
        $create['type'] = '1';

        $user = User::create($create);
        $user->permissions()->attach(array_map(static function ($item) {
            return ['permission_id' => $item];
        }, $permissions));

        $userResource = new UserResource($user);
        return $this->sendResponse($userResource, __('messages.user_created'));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'max:255',
            'username' => 'max:255|unique:users,username,' . $user->id,
            "photo" => 'image|mimes:png,jpg|max:5000',
            'password' => 'min:8',
            'confirm_password' => 'same:password',
            'permissions' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $permissions = [];
        if ($request->get('permissions'))
            $permissions = explode(',', $request->get('permissions'));
        $validator = Validator::make(['permissions' => $permissions], [
            'permissions' => 'required|array',
            'permissions.*' => 'required|exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('messages.error_validation'), $validator->errors());
        }

        $edit = $request->except('permissions');


        if (isset($edit["photo"])) {
            Storage::disk('users')->delete($user->photo);
            $request->file('photo')->store('users');
            $edit["photo"] = $request->file('photo')->hashName();
        }

        if (isset($edit['password'])) {
            $edit['password'] = Hash::make($edit['password']);
        }

        $user->update($edit);
        $user->permissions()->sync($permissions);

        $userResource = new UserResource($user);
        return $this->sendResponse($userResource, __('messages.user_updated'));
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->status === "1") {
            $user->update(["status" => "0"]);
        }

        return $this->sendResponse([], __('messages.user_deleted'));
    }
}
