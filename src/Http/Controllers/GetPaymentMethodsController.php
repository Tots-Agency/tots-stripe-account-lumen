<?php

namespace Tots\StripeAccount\Http\Controllers;

use Illuminate\Http\Request;
use Tots\Account\Models\TotsAccount;
use Tots\Stripe\Services\TotsStripeService;

class GetPaymentMethodsController extends \Laravel\Lumen\Routing\Controller
{
    /**
     *
     * @var TotsStripeService
     */
    protected $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TotsStripeService $service)
    {
        $this->service = $service;
    }
    
    public function handle(Request $request)
    {
        /** @var \Tots\Account\Models\TotsAccount $account */
        $account = $request->input(TotsAccount::class);
        /** Return payment methods */
        return $this->service->getPaymentMethodsSaved($account->id);
    }
}