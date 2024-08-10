<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Resources\PermissionCollection;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends BaseController
{
    public function index(): JsonResponse
    {

        $permissions = Permission::orderBy('name')->get();
        $permissions = new PermissionCollection($permissions);

        return $this->sendResponse($permissions, __('messages.found'));
    }
}
