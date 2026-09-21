<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $this->authorize('viewAny', ContactMessage::class);

        $query = ContactMessage::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->has('unread')) {
            $request->boolean('unread')
                ? $query->whereNull('read_at')
                : $query->whereNotNull('read_at');
        }

        $messages = $query->orderByDesc('created_at')->paginate(20);

        return ContactMessageResource::collection($messages)->additional([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function show(ContactMessage $contactMessage)
    {
        $this->authorize('view', $contactMessage);

        if (!$contactMessage->read_at) {
            $contactMessage->update(['read_at' => now()]);
        }

        return $this->success(new ContactMessageResource($contactMessage));
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $this->authorize('delete', $contactMessage);

        $contactMessage->delete();

        return $this->success(null, 'Message deleted.');
    }
}
