<?php

namespace App\Http\Controllers;

use App\Services\MailService;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function __construct(private MailService $mailService)
    {
    }

    public function basicEmail(Request $request)
    {
        $this->mailService->sendEmailConfirmation([$request->user['email']], ['name' => $request->user['name']], 'Email Confirmation');

        echo response("Basic Email Sent. Check your inbox.", 201);
    }
}
