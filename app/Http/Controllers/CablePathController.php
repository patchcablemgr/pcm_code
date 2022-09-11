<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\CablePathModel;
use App\Models\LocationModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class CablePathController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cablePaths = CablePathModel::all();

        return $cablePaths;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $validatorInput = [
            'cabinet_a_id' => $request->cabinet_a_id,
            'cabinet_b_id' => $request->cabinet_b_id,
            'distance' => $request->distance,
            'notes' => $request->notes,
        ];

        $validatorRules = [
            'cabinet_a_id' => [
                'required',
                'integer',
                'exists:location,id',
                'different:cabinet_b_id'
            ],
            'cabinet_b_id' => [
                'required',
                'integer',
                'exists:location,id',
                'different:cabinet_a_id'
            ],
            'distance' => [
                'required',
                'integer',
                'between:1,1000'
            ],
            'notes' => [
                'string',
                'nullable',
                'max:255'
            ],
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Validate location is type cabinet
        $cabinetA = LocationModel::where('id', $request->cabinet_a_id)->first();
        if($cabinetA->type != "cabinet") {
            $Err = ['cabinet_a_id' => 'Location must be type cabinet.'];
            throw ValidationException::withMessages($Err);
        }

        // Validate location is type cabinet
        $cabinetB = LocationModel::where('id', $request->cabinet_b_id)->first();
        if($cabinetB->type != "cabinet") {
            $Err = ['cabinet_b_id' => 'Location must be type cabinet.'];
            throw ValidationException::withMessages($Err);
        }

        $cabinetAID = $request->cabinet_a_id;
        $cabinetBID = $request->cabinet_b_id;
        $distance = $request->distance;
        $notes = ($request->notes) ? $request->notes : "";

        $cablePath = new CablePathModel;

        $cablePath->cabinet_a_id = $cabinetAID;
        $cablePath->cabinet_b_id = $cabinetBID;
        $cablePath->distance = $distance;
        $cablePath->notes = $notes;

        $cablePath->save();

        return $cablePath;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }
        
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:cable_path'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $cablePath = CablePathModel::where('id', $id)->first();

        $cablePath->delete();

        return array('id' => $id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $validatorInput = [
            'id' => $id,
            'cabinet_a_id' => $request->cabinet_a_id,
            'cabinet_b_id' => $request->cabinet_b_id,
            'distance' => $request->distance,
            'notes' => $request->notes,
        ];

        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:cable_path,id'
            ],
            'cabinet_a_id' => [
                'required',
                'integer',
                'exists:location,id',
                'different:cabinet_b_id'
            ],
            'cabinet_b_id' => [
                'required',
                'integer',
                'exists:location,id',
                'different:cabinet_b_id'
            ],
            'distance' => [
                'required',
                'integer',
                'between:1,1000'
            ],
            'notes' => [
                'string',
                'nullable',
                'max:255'
            ],
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Validate location is type cabinet
        $cabinetA = LocationModel::where('id', $request->cabinet_a_id)->first();
        if($cabinetA->type != "cabinet") {
            $erErrrMsg = ['cabinet_a_id' => 'Location must be type cabinet.'];
            throw ValidationException::withMessages($Err);
        }

        // Validate location is type cabinet
        $cabinetB = LocationModel::where('id', $request->cabinet_a_id)->first();
        if($cabinetA->type != "cabinet") {
            $erErrrMsg = ['cabinet_a_id' => 'Location must be type cabinet.'];
            throw ValidationException::withMessages($Err);
        }

        // Retrieve record
        $cablePath = CablePathModel::where('id', $id)->first();

        // Store request data
        $data = $request->all();

        // Update object record
        foreach($data as $key => $value) {
            if($key == 'cabinet_a_id') {
                $cablePath->cabinet_a_id = $value;
            } else if($key == 'cabinet_b_id') {
                $cablePath->cabinet_b_id = $value;
            } else if($key == 'distance') {
                $cablePath->distance = $value;
            }else if($key == 'notes') {
                $cablePath->notes = ($value) ? $value : "";
            }
        }

        $cablePath->save();

        return $cablePath;
    }
}
