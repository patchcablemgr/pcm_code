<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TenantModel;
use App\Models\ForgotPasswordModel;
//use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
    * Create user
    *
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] password_confirmation
    * @return [string] message
    */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email'=>'required|string|email|unique:users|max:255',
            'password'=>'required|string',
            'c_password' => 'required|same:password'
        ]);

		if(User::count() === 0) {
			$status = true;
			$role = 'admin';
		} else {
			$status = false;
			$role = 'operator';
		}

        $user = new User([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
			'status' => $status,
			'role' => $role,
        ]);

        if($user->save()){
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
            'message' => 'Successfully created user!',
            'accessToken'=> $token,
            ],201);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
    }

	/**
    * Create tenant
    *
	* @param  [string] tenant
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] password_confirmation
    * @return [string] message
    */
    public function registerTenant(Request $request)
    {

		$reservedSubDomains = [
			'admin',
			'register',
			'home',
			'api',
			'backend',
			'pcm'
		];

        $request->validate([
			'tenant' => [
				'required',
				'string',
				'max:63',
				'regex:/^[a-z0-9][a-z0-9\-]*$/',
				Rule::notIn($reservedSubDomains),
			],
            'name' => [
				'required',
				'string',
				'max:255',
			],
            'email'=>'required|string|email|unique:users',
            'password'=>'required|string',
            'c_password' => 'required|same:password'
        ]);
		
		if (!TenantModel::where('id', '=', $request->tenant)->exists()) {

			$userData = array(
				'name' => $request->name,
				'email' => $request->email,
				'password' => bcrypt($request->password),
				'status' => true,
				'role' => 'admin'
			);

			$tenant = TenantModel::create([
				'id' => $request->tenant,
				'initial_user_data' => $userData,
				'initial_host' => $request->header('tenant-domain'),
				'tenancy_db_username' => 'user_'.$request->tenant,
				'tenancy_db_password' => Str::random(40),
			]);

			return $tenant->run(function($tenant) {
				
				$user = new User($tenant->initial_user_data);
				$host = $tenant->initial_host;
				$tenant->initial_user_data = null;
				$tenant->initial_host = null;
				$tenant->save();
	
				if($user->save()) {
	
					return response()->json([
						'tenant-url' => $tenant->id.'.'.$host
					],201);
				} else {
					$tenant->delete();
					return response()->json(['error'=>'Provide proper details'],422);
				}
			});
		} else {
			return response()->json(['error'=>'Tenant already exists'],422);
		}
    }
	
	/**
	* Login user and create token
	*
	* @param  [string] email
	* @param  [string] password
	* @param  [boolean] remember_me
	*/

	public function login(Request $request)
	{
		$request->validate([
		'email' => 'required|string|email',
		'password' => 'required|string',
		]);

		$credentials = request(['email','password']);
		if(!Auth::attempt($credentials)) {
			return response()->json([
				'message' => 'Unauthorized'
			],401);
		}

		$user = $request->user();

		// Check if user is active
		if(!$user['status']) {
			return response()->json([
				'message' => 'Account is inactive.  Please contact your administrator.'
			],401);
		}

		// Check if user has MFA enabled
		if($user['mfa_enabled']) {

			// Generate MFA session data
			$mfaSessionHash = bin2hex(random_bytes(20));
			$mfaSessionExpiration = time() + 300;

			// Retrieve User
			$userID = $user['id'];
        	$user = User::where('id', $userID)->first();

			// Store MFA session data
			$user->mfa_session_hash = $mfaSessionHash;
			$user->mfa_session_expiration = $mfaSessionExpiration;
			$user->save();

			return response()->json([
				'mfa_session_hash' => $mfaSessionHash,
			]);
		}

		$tokenResult = $user->createToken('Personal Access Token');
		$token = $tokenResult->plainTextToken;

		if($user->role == 'user') {
			$role = 'User';
			$ability = array(
				array('action' => 'read', 'subject' => 'Public'),
				array('action' => 'read', 'subject' => 'User')
			);
		} else if($user->role == 'operator') {
			$role = 'Operator';
			$ability = array(
				array('action' => 'read', 'subject' => 'Public'),
				array('action' => 'read', 'subject' => 'User'),
				array('action' => 'read', 'subject' => 'Operator')
			);
		} else if($user->role == 'admin') {
			$role = 'Administrator';
			$ability = array(
				array('action' => 'read', 'subject' => 'Public'),
				array('action' => 'read', 'subject' => 'User'),
				array('action' => 'read', 'subject' => 'Operator'),
				array('action' => 'read', 'subject' => 'Administrator'),
			);
		}

		return response()->json([
			'accessToken' => $token,
			'token_type' => 'Bearer',
			'ability' => $ability,
			'role' => $role,
			'username' => $user->name,
		]);
	}

	/**
	* Send forgot password link to user
	*
	* @param  [string] email
	*/

	public function forgotPassword(Request $request)
	{
		$request->validate(['email' => 'required|email']);

		if (User::where('email', '=', $request->email)->count() > 0) {

			$user = User::where('email', '=', $request->email)->first();

			// Generate password reset code
			$code = Str::random(10);

			$response = Http::asForm()
			->withOptions([
				'headers' => [
					'Accept' => 'application/json',
				]
			])
			->post('https://pcm.patchcablemgr.com/api/forgot-password', [
				'email' => $request->email,
				'code' => $code,
			]);

			if($response->status() !== 200) {
				throw ValidationException::withMessages(['forgot-password' => $response->body()]);
			} else {

				// Create new forgot_password entry
				$forgotPassword = new ForgotPasswordModel;

				// Store forgot_password data
				$forgotPassword->email = $request->email;
				$forgotPassword->user_id = $user['id'];
				$forgotPassword->code = $code;
				$forgotPassword->created_timestamp = time();

				// Save forgot_password entry
				$forgotPassword->save();
			}
		}
 
        return true;
	}

	/**
	* Reset password
	*
	* @param  [string] code
	* @param  [string] password
	*/

	public function resetPassword(Request $request)
	{
		$request->validate([
			'code' => 'required|string',
			'password' => 'required|string',
		]);

		if (ForgotPasswordModel::where('code', '=', $request->code)->count() > 0) {

			$forgotPassword = ForgotPasswordModel::where('code', '=', $request->code)->first();
			if(($forgotPassword['created_timestamp']+300) > time() && !$forgotPassword['used']) {
				if (User::where('id', '=', $forgotPassword['user_id'])->count() > 0) {

					// Reset user password
					$user = User::where('id', '=', $forgotPassword['user_id'])->first();
					$user->forceFill([
						'password' => Hash::make($request->password),
					]);
					$user->save();

					// Clear user sessions
					$user->tokens()->delete();

					// Mark pasword reset as used
					$forgotPassword->used = true;
					$forgotPassword->save();
				} else {
					throw ValidationException::withMessages(['code' => 'Code is not associated with a valid user.']);
				}
			} else {
				throw ValidationException::withMessages(['code' => 'Code has expired.']);
			}
		} else {
			throw ValidationException::withMessages(['code' => 'Code is invalid.']);
		}
 
        return true;
	}

	/**
	* Login user and create token
	*
	* @param  [string] mfa_session_hash
	* @param  [integer] otp
	*/

	public function mfa(Request $request)
	{
		$request->validate([
		'mfa_session_hash' => 'required|string|size:40|exists:users,mfa_session_hash',
		'otp' => 'required|integer|digits:6',
		]);

		// Collect input
		$mfaSessionHash = $request->mfa_session_hash;
		$otp = $request->otp;

		// Retrieve user entry
		$userEntry = User::where('mfa_session_hash', $mfaSessionHash)->first();
		$userID = $userEntry->id;

		// Initialize google MFA
        $google2fa = new Google2FA();

        // Retrieve MFA data
        $mfaSessionExpiration = $userEntry->mfa_session_expiration;
		$mfaSecret = $userEntry->mfa_secret;

		// Check MFA session expiry
		if(time() > $mfaSessionExpiration) {
			return response()->json([
				'message' => 'MFA session has expired'
			],401);
		}

        // Validate OTP
        $valid = $google2fa->verifyKey($mfaSecret, $otp);

		if(!$valid) {
			return response()->json([
				'message' => 'MFA validation failed'
			],401);
		}

		Auth::loginUsingId($userID);

		$user = $request->user();

		$tokenResult = $user->createToken('Personal Access Token');
		$token = $tokenResult->plainTextToken;

		if($user->role == 'user') {
			$role = 'User';
			$ability = array(
				array('action' => 'read', 'subject' => 'Public'),
				array('action' => 'read', 'subject' => 'User')
			);
		} else if($user->role == 'operator') {
			$role = 'Operator';
			$ability = array(
				array('action' => 'read', 'subject' => 'Public'),
				array('action' => 'read', 'subject' => 'User'),
				array('action' => 'read', 'subject' => 'Operator')
			);
		} else if($user->role == 'admin') {
			$role = 'Administrator';
			$ability = array(
				array('action' => 'read', 'subject' => 'Public'),
				array('action' => 'read', 'subject' => 'User'),
				array('action' => 'read', 'subject' => 'Operator'),
				array('action' => 'read', 'subject' => 'Administrator'),
			);
		}

		return response()->json([
			'accessToken' => $token,
			'token_type' => 'Bearer',
			'ability' => $ability,
			'role' => $role,
			'username' => $user->name,
		]);
	}
	
	/**
	* Get the authenticated User
	*
	* @return [json] user object
	*/
	public function user(Request $request)
	{
		return response()->json($request->user());
	}
	
	/**
	* Logout user (Revoke the token)
	*
	* @return [string] message
	*/
	public function logout(Request $request)
	{
		$request->user()->tokens()->delete();

		return response()->json([
		'message' => 'Successfully logged out'
		]);

	}
}