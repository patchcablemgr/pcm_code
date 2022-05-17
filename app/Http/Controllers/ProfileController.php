<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Generate 2FA QR Code
     *
     * @return \Illuminate\Http\Response
     */
    public function generateMFAQRCode(Request $request)
    {

        // Get user ID
        $user = $request->user();
        $userID = $user['id'];
        
        // Initialize google MFA
        $google2fa = new Google2FA();

        // Retrieve user
        $user = User::where('id', $userID)->first();

        $companyName = 'PatchCableMgr';
        $companyEmail = $user->email;
    
        // Generate MFA QR code
        $secretKey = $google2fa->generateSecretKey();
        $inlineURL = $google2fa->getQRCodeInline(
            $companyName,
            $companyEmail,
            $secretKey
        );

        // Store secret key temporarily
        $user->mfa_secret_temp = $secretKey;
        $user->save();

        //return $inlineURL;
        $imgBase64 = base64_encode($inlineURL);
        return 'data:image/svg+xml;base64,'.$imgBase64;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmMFA(Request $request)
    {

        // Store OTP
        $otp = $request->input('otp');
        
        // Get user ID
        $user = $request->user();
        $userID = $user['id'];

        // Initialize google MFA
        $google2fa = new Google2FA();

        // Retrieve user
        $user = User::where('id', $userID)->first();
        $mfaSecretTemp = $user->mfa_secret_temp;

        // Validate OTP
        $valid = $google2fa->verifyKey($mfaSecretTemp, $otp);

        if($valid) {
            $user->mfa_enabled = true;
            $user->mfa_secret = $mfaSecretTemp;
            $user->mfa_secret_temp = null;
            $user->save();
        }

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function disableMFA(Request $request)
    {
        
        // Get user ID
        $user = $request->user();
        $userID = $user['id'];

        // Retrieve user
        $user = User::where('id', $userID)->first();

        $user->mfa_enabled = false;
        $user->mfa_secret = null;
        $user->mfa_secret_temp = null;
        $user->save();

        return $user;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
