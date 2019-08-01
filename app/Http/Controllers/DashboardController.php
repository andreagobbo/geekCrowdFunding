<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Payment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard(){
        
        $user = Auth::user();
        
        $title = trans('app.dashboard');
        $user_count = User::all()->count();
        $pending_campaign_count = Campaign::where('status',0)->count();
        $blocked_campaign_count = Campaign::where('status',2)->count();
        $active_campaign_count = Campaign::where('status',1)->count();
        
        $payment_created = Payment::whereStatus('success')->count();
        $payment_amount = Payment::whereStatus('success')->sum('amount');


        if ($user->is_admin()){
            $pending_campaigns = Campaign::where('status', 0)->orderBy('id', 'desc')->take(10)->get();
            $last_payments = Payment::whereStatus('success')->orderBy('id', 'desc')->take(10)->get();

        }else{
            $campaign_ids = $user->my_campaigns()->pluck('id')->toArray();

            $pending_campaigns = Campaign::where('status', 0)->whereUserId($user->id)->orderBy('id', 'desc')->take(10)->get();

            $last_payments = Payment::whereStatus('success')->whereIn('campaign_id', $campaign_ids)->orderBy('id', 'desc')->take(10)->get();

        }


        return view('admin.dashboard', compact('title','user_count', 'active_campaign_count', 'pending_campaign_count', 'blocked_campaign_count', 'payment_created', 'payment_amount', 'pending_campaigns', 'last_payments'));
    }
}
