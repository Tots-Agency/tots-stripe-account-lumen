<?php

namespace Tots\StripeAccount\Http\Controllers;

use Illuminate\Http\Request;
use Tots\Account\Models\TotsAccount;
use Tots\Auth\Models\TotsProvider;
use Tots\Billing\Models\TotsAccountProvider;
use Tots\Stripe\Services\TotsStripeService;

class RemovePaymentMethodController extends \Laravel\Lumen\Routing\Controller
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
    
    public function handle($id, Request $request)
    {
        /** @var \Tots\Account\Models\TotsAccount $account */
        $account = $request->input(TotsAccount::class);
        /** @var TotsProvider $account */
        $provider = $request->input(TotsProvider::class);
        /** Search exist Customer created */
        $providerAcc = TotsAccountProvider::where('account_id', $account->id)->where('provider_id', $provider->id)->first();
        if($providerAcc === null){
            throw new \Exception('Customer not found');
        }
        /** Return payment methods */
        $methods = $this->service->getPaymentMethodsSaved($providerAcc->external_id);
        /** Verify if exist */
        foreach($methods['data'] as $method) {
            if($method['id'] === $id) {
                $this->service->removePaymentMethodById($id);
                return true;
            }
        }

        throw new \Exception('Payment method not found');
    }
}