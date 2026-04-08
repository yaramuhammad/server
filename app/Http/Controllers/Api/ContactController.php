<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:160'],
            'phone'   => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $adminEmail = config('mail.admin_address') ?: env('ADMIN_EMAIL');

        if (!$adminEmail) {
            return response()->json([
                'message' => 'Admin email is not configured.',
            ], 500);
        }

        Mail::to($adminEmail)->queue(new ContactMessageMail(
            senderName: $data['name'],
            senderEmail: $data['email'],
            senderPhone: $data['phone'] ?? null,
            senderSubject: $data['subject'] ?? null,
            messageBody: $data['message'],
        ));

        return response()->json([
            'message' => 'Your message has been sent successfully.',
        ]);
    }
}
