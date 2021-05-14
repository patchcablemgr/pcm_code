<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
		
		// Retrieve user record
		$attribute = 'email';
		$email = $request->email;
		$users = User::all();
		$user = $users->filter(function ($item) use ($attribute, $email) {
			return strtolower($item[$attribute]) == strtolower($email);
		})->values();
		
		// Evaluate user status
		if($user->count()) {
			if(!$user[0]->active) {
				throw ValidationException::withMessages([
					'email' => __('auth.inactive'),
				]);
				Log::info('Inactive');
			}
		}
		
        return $next($request);
    }
}
