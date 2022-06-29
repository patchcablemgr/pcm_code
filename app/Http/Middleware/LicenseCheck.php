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

        $organization = OrganizationModel::first();
        $licenseKey = $organization->license_key;
        $appID = $organization->app_id;
        $licenseLastChecked = $organization->license_last_checked;

        if($licenseKey !== null && $appID !== null && $licenseLastChecked !== null) {
            if(($licenseLastChecked+86400) < time()) {
                $PCM = new PCM;
                $licenseData = $PCM->fetchLicenseData($licenseKey, $appID);

                if($licenseData->status() == 200) {
                    $organization->license_last_checked = time();
                    $organization->license_data = $licenseData->json();
                    $organization->save();
                }
            }
        }

        return $next($request);
    }
}
