<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function retrievePlans()
    {
        $key = \config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);
        $plansRaw = $stripe->plans->all();
        $plans = $plansRaw->data;

        foreach ($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,
                []
            );
            $plan->product = $prod;
        }
        return $plans;
    }

    public function index()
    {
        $key = \config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);

        $subscriptions = $stripe->subscriptions->all();

        return view('subscriptions.index',[
            'subscriptions' => $subscriptions
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        return view('subscriptions.create', [
            'user' => $user,
            'intent' => $user->createSetupIntent(),
            'plans' => $this->retrievePlans()
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');
        $plan = $request->input('plan');

        try {
            $user->createOrGetStripeCustomer();
            $user->addPaymentMethod($paymentMethod);
            
            $user->newSubscription('default', $plan)->create($paymentMethod, [
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }

        return redirect()->route('subscriptions.index')->with('status','Subscription is completed.');
    }

    public function show(Request $request, $stripe_id)
    {
        $key = \config('services.stripe.secret');
        $stripe = new \Stripe\StripeClient($key);

        $retrieve_subscription = $stripe->subscriptions->retrieve(
            $stripe_id,
            []
        );

        return view('subscriptions.show',[
            'retrieve_subscription' => $retrieve_subscription
        ]);
    }

    public function cancel(Request $request, $stripe_id)
    {
        //$user = User::where('stripe_id',$stripe_id)->first();
        $subscription = Subscription::with('user')->where('stripe_id',$stripe_id)->first();

        if($subscription && $subscription->user) {
            $user = $subscription->user;
            $user->subscription('default')->cancel();

            return back()->with('status','Subscription is canceled.');
        }

        return back()->with('status','Something went wrong.');
    }

    public function resume(Request $request, $stripe_id)
    {
        //$user = User::where('stripe_id',$stripe_id)->first();
        $subscription = Subscription::with('user')->where('stripe_id',$stripe_id)->first();

        if($subscription && $subscription->user) {
            $user = $subscription->user;
            $user->subscription('default')->resume();

            return back()->with('status','Subscription is resumed.');
        }

        return back()->with('status','Something went wrong.');
    }
}
