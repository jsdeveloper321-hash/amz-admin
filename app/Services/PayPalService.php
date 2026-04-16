<?php

namespace App\Services;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class PayPalService
{
    private $apiContext;

    public function __construct()
    {
        $paypalConfig = config('paypal');
        
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $paypalConfig['client_id'],
                $paypalConfig['secret']
            )
        );
        $this->apiContext->setConfig($paypalConfig['settings']);
    }

    public function createPayment($amount)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amountInstance = new Amount();
        $amountInstance->setTotal($amount);
        $amountInstance->setCurrency('USD');

        $transaction = new Transaction();
        $transaction->setAmount($amountInstance);
        $transaction->setDescription('Payment description');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('payment.success'))
                     ->setCancelUrl(route('payment.cancel'));

        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->apiContext);
            return $payment;
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            return $ex->getData();
        }
    }

    public function executePayment($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->apiContext);
            return $result;
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            return $ex->getData();
        }
    }
}
