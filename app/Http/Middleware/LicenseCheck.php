<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\PCM;
use App\Models\OrganizationModel;

class LicenseCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $organization = OrganizationModel::where('id', '=', '*')->first();
        $licenseLastChecked = $organization['license_last_checked'];
        $appID = $organization['app_id'];

        if(($licenseLastChecked+86400) > time()) {
            $PCM = new PCM;
            $PCM->fetchLicenseData();
        }

        return $next($request);
    }
}
