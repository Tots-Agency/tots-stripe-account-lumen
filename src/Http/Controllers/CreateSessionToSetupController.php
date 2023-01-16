<?php

namespace Tots\StripeAccount\Http\Controllers;

use Illuminate\Http\Request;
use Tots\Account\Models\TotsAccount;
use Tots\Auth\Models\TotsProvider;
use Tots\Billing\Models\TotsAccountProvider;
use Tots\Stripe\Services\TotsStripeService;

class CreateSessionToSetupController extends \Laravel\Lumen\Routing\Controller
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
        /** @var TotsProvider $account */
        $provider = $request->input(TotsProvider::class);
        /** Search exist Customer created */
        $providerAcc = TotsAccountProvider::where('account_id', $account->id)->where('provider_id', $provider->id)->first();
        if($providerAcc === null){
            // Create customerId
            $customerStripe = $this->service->createCustomerByName($account->title);

            $providerAcc = new TotsAccountProvider();
            $providerAcc->account_id = $account->id;
            $providerAcc->provider_id = $provider->id;
            $providerAcc->external_id = $customerStripe->id;
            $providerAcc->save();
        }
        /** Generate Session */
        return $this->service->createModeSetupSessionCheckout($providerAcc->external_id, env('STRIPE_SETUP_SUCCESS_URL', ''), env('STRIPE_SETUP_CANCEL_URL', ''));
    }
}