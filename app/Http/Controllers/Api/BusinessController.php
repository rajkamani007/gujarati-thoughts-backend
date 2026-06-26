<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Business::orderBy('name')->get()]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Business::findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:4096',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        unset($data['logo']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('businesses', 'public');
        }
        $item = Business::create($data);
        return response()->json(['message' => 'Business created', 'data' => $item], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = Business::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|image|max:4096',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'boolean',
        ]);
        unset($data['logo']);
        if ($request->hasFile('logo')) {
            if ($item->logo) Storage::disk('public')->delete($item->logo);
            $data['logo'] = $request->file('logo')->store('businesses', 'public');
        }
        if ($request->has('status')) $data['status'] = $request->boolean('status');
        $item->update($data);
        return response()->json(['message' => 'Business updated', 'data' => $item]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = Business::findOrFail($id);
        if ($item->logo) Storage::disk('public')->delete($item->logo);
        $item->delete();
        return response()->json(['message' => 'Business deleted']);
    }
}
