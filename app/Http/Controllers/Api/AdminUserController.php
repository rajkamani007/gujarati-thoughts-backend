<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\AdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminUserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = AdminUser::orderBy('name')->get();

        return AdminUserResource::collection($users);
    }

    public function store(StoreAdminUserRequest $request): JsonResponse
    {
        $user = AdminUser::create($request->validated());

        return response()->json([
            'message' => 'Admin user created successfully',
            'data' => new AdminUserResource($user),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = AdminUser::findOrFail($id);

        return response()->json([
            'data' => new AdminUserResource($user),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, int $id): JsonResponse
    {
        $user = AdminUser::findOrFail($id);
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password'], $data['password_confirmation']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Admin user updated successfully',
            'data' => new AdminUserResource($user),
        ]);
    }
}
