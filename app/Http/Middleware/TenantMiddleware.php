<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('restaurant_slug');

        if ($slug) {
            $restaurant = Restaurant::where('slug', $slug)->first();

            if (!$restaurant) {
                abort(404, 'Restaurant not found.');
            }

            // Check if subscription has expired or is inactive
            if ($restaurant->subscription_status !== 'active' && now()->greaterThan($restaurant->subscription_expires_at)) {
                abort(403, "This restaurant's subscription is currently inactive.");
            }

            // Share current restaurant across the application
            app()->instance('current_restaurant', $restaurant);
            view()->share('current_restaurant', $restaurant);
        }

        return $next($request);
    }
}
