<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $keyId = env('RAZORPAY_KEY_ID');
        $keySecret = env('RAZORPAY_KEY_SECRET');
        $this->api = new Api($keyId, $keySecret);
    }

    public function createOrder($amount, $currency = 'INR')
    {
        return $this->api->order->create([
            'amount' => $amount * 100, // amount in paise
            'currency' => $currency,
            'payment_capture' => 1
        ]);
    }

    public function capturePayment($paymentId, $amount)
    {
        return $this->api->payment->fetch($paymentId)->capture([
            'amount' => $amount * 100
        ]);
    }
}
