<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;
//use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
//use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class TenancyWrapper
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
        $appDeployment = env('APP_DEPLOYMENT');

        if($appDeployment === 'hosted' and !$request->is('api/auth/register-tenant') and !$request->is('register-tenant')) {

            //First you need to instantiate the middleware you want to use and call the handle method on it.
            //Then you have to create a closure where you'll do all your logic.
            return app(InitializeTenancyByRequestData::class)->handle($request, function ($request) use ($next) {
                return $next($request);
            });
            /*
            return app(InitializeTenancyByDomain::class)->handle($request, function ($request) use ($next) {
                return app(PreventAccessFromCentralDomains::class)->handle($request, function ($request) use ($next) {
                    //Put your awesome stuff there. Like:
        
                    return $next($request);
                });
            });
            */

        } else {
            return $next($request);
        }
    }
}
