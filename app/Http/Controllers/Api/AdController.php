<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Ad::orderBy('position')->orderBy('name')->get()]);
    }

    public function publicIndex(): JsonResponse
    {
        $ads = Ad::where('status', true)->get()->keyBy('position');
        return response()->json(['data' => $ads]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Ad::findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'ad_code' => 'nullable|string',
            'status' => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        $item = Ad::create($data);
        return response()->json(['message' => 'Ad created', 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = Ad::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:50',
            'ad_code' => 'nullable|string',
            'status' => 'boolean',
        ]);
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $item->update($data);
        return response()->json(['message' => 'Ad updated', 'data' => $item]);
    }

    public function destroy(int $id): JsonResponse
    {
        Ad::findOrFail($id)->delete();
        return response()->json(['message' => 'Ad deleted']);
    }
}
