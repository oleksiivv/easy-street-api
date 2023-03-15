<?php

namespace App\Services;

use Stripe;

class PaymentService
{
    const CENTS_TO_DOLLARS_MULTIPLIER = 100;

    public function init(): void
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * @throws Stripe\Exception\ApiErrorException
     */
    public function pay(string $stripeToken, int $amount, array $data): void
    {
        Stripe\Charge::create(array_filter([
            'amount' => $amount * self::CENTS_TO_DOLLARS_MULTIPLIER,
            'currency' => config('services.currency'),
            'customer' => $stripeToken,
            'description' => data_get($data, 'description'),
        ]));
    }

    public function createCustomer(array $data): Stripe\Customer
    {
        $token = Stripe\Token::create([
            'card' => array_merge($data['card'], ['name' => $data['name']]),
        ]);

        $customer = Stripe\Customer::create([
            'address' => data_get($data, 'address'),
            'email' => data_get($data, 'email'),
            'name' => data_get($data, 'name'),
            'source' => $token,
        ]);

        return $customer;
    }
}
