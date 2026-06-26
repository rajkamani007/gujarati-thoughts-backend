<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(ContactRequest $request): JsonResponse
    {
        $data = $request->validated();
        $contact = Contact::create($data);

        return response()->json([
            'message' => 'Thank you for your message! We will get back to you soon.',
            'data' => [
                'name' => $contact->name,
                'email' => $contact->email,
                'subject' => $contact->subject,
            ],
        ], 201);
    }

    public function index(): JsonResponse
    {
        $contacts = Contact::latest()->paginate(20);
        return response()->json($contacts);
    }

    public function show(int $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }
        return response()->json(['data' => $contact]);
    }

    public function destroy(int $id): JsonResponse
    {
        Contact::findOrFail($id)->delete();
        return response()->json(['message' => 'Inquiry deleted']);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => $request->boolean('is_read', true)]);
        return response()->json(['message' => 'Updated', 'data' => $contact]);
    }
}
