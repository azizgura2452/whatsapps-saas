<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Business;

class SetBusinessContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user) {
            // Get business from session or user's first business
            $businessId = session('current_business_id');
            
            if ($businessId) {
                $business = Business::find($businessId);
            } else {
                $business = $user->businesses()->first();
            }
            
            if ($business) {
                app()->instance('current_business', $business);
                view()->share('currentBusiness', $business);
            }
        }
        
        return $next($request);
    }
}