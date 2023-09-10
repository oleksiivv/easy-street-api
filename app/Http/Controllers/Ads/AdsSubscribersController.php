<?php

namespace App\Http\Controllers\Ads;

use App\Http\Controllers\Controller;
use App\Models\AdsSubscriber;
use Illuminate\Http\Response;

class AdsSubscribersController extends Controller
{
    public function subscribe(int $adminId, string $email): Response
    {
        $data = [
            'admin_id' => $adminId,
            'email' => $email
        ];

        AdsSubscriber::firstOrCreate($data, $data);

        return response()->noContent();
    }

    public function index(int $adminId): Response
    {
        return new Response(AdsSubscriber::where([
            'admin_id' => $adminId,
        ]));
    }
}
