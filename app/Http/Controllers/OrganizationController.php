<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganizationModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Gate;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization = OrganizationModel::first();

        return $organization;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLicense()
    {

        // RBAC
        if (! Gate::allows('admin')) {
            abort(403);
        }

        $organization = OrganizationModel::first();
        $licenseKey = $organization->license_key;
        $appID = $organization->app_id;

        if($licenseKey == null) {
            throw ValidationException::withMessages(['license_key' => 'Invalid license key.']);
        }

        if($appID == null) {
            throw ValidationException::withMessages(['app_id' => 'Invalid app ID.']);
        }

        $PCM = new PCM;
        $licenseData = $PCM->fetchLicenseData($licenseKey, $appID);

        if($licenseData->status() == 200) {
            $organization->license_last_checked = time();
            $organization->license_data = $licenseData->json();
            $organization->save();
            return $organization;
        } else {
            throw ValidationException::withMessages(['license-server' => $licenseData->body()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLicensePortal()
    {

        // RBAC
        if (! Gate::allows('admin')) {
            abort(403);
        }

        $organization = OrganizationModel::first();
        $licenseKey = $organization->license_key;
        $appID = $organization->app_id;

        if($licenseKey == null) {
            throw ValidationException::withMessages(['license_key' => 'Invalid license key.']);
        }

        if($appID == null) {
            throw ValidationException::withMessages(['app_id' => 'Invalid app ID.']);
        }

        $PCM = new PCM;
        $licensePortal = $PCM->fetchLicensePortal($licenseKey, $appID);

        if($licensePortal->status() == 200) {
            return $licensePortal->body();
        } else {
            throw ValidationException::withMessages(['license-server' => $licensePortal->body()]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLicense(Request $request)
    {

        // RBAC
        if (! Gate::allows('admin')) {
            abort(403);
        }

        $validatorInput = [
            'license-key' => $request->license_key
        ];

        $validatorRules = [
            'license-key' => [
                'required',
                'regex:/^[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4}$/i'
            ]
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $licenseKey = $request['license_key'];

        $organization = OrganizationModel::first();
        $appID = $organization->app_id;

        $PCM = new PCM;
        $licenseData = $PCM->fetchLicenseData($licenseKey, $appID);

        if($licenseData->status() == 200) {
            $organization->license_last_checked = time();
            $organization->license_key = $licenseKey;
            $organization->license_data = $licenseData->json();
            $organization->save();
            return $organization;
        } else {
            throw ValidationException::withMessages(['license-server' => $licenseData->body()]);
        }

    }
}
