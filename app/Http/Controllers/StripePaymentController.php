<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use PHPUnit\Exception;
use Stripe;

class StripePaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    public function stripePost(Request $request)
    {
        try {
            $this->paymentService->pay($request->stripe_token, $request->amount, []);
        } catch (Exception) {
            throw new \HttpException("The payment service is temporary unavailable");
        }

        return response(Response::HTTP_ACCEPTED);
    }
}
