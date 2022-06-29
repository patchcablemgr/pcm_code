<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // RBAC
        if (! Gate::allows('admin')) {
            abort(403);
        }

        $users = User::all();

        return $users;
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

        // RBAC
        if (! Gate::allows('admin')) {
            abort(403);
        }
        
        // Validate
        $validatorInput = [
            'id' => $id,
        ];
        $validatorRules = [
            'id' => [
                'required',
                'numeric',
                'exists:users',
            ],
        ];

        // Validate user status
        if($request->status) {
            $validatorInput = array_merge($validatorInput, array(
                'status' => $request->status
            ));
            $validatorRules = array_merge($validatorRules, array(
                'status' => [
                    'boolean',
                ]
            ));
        }

        // Validate user role
        if($request->role) {
            $validatorInput = array_merge($validatorInput, array(
                'role' => $request->role
            ));
            $validatorRules = array_merge($validatorRules, array(
                'role' => [
                    'in:admin,operator,user',
                ]
            ));
        }

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Validate user is not self
        $userID = Auth::id();
        if($userID == $id) {
            throw ValidationException::withMessages(['id' => 'Cannot update your own account.']);
        }

        // Store user
        $user = User::where('id', $id)->first();

        // Update details
        foreach($request->all() as $field => $value) {
            switch($field) {
                case 'status':
                    $user->status = $value;

                    // Revoke user tokens if necessary
                    if(!$value) {
                        $user->tokens()->delete();
                    }
                    break;

                case 'role':
                    $user->role = $value;
                    break;
            }
        }

        $user->save();

        return $user->toArray();
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
