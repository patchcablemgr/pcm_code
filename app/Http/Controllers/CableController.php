<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use App\Models\CableModel;
use App\Models\CableConnectorModel;
use App\Models\MediaModel;
use App\Models\MediaTypeModel;
use Illuminate\Support\Facades\Gate;

class CableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $connections = CableModel::all();

        return $connections;
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

        $PCM = new PCM;

        // Store request data
        $validatorRules = [
            'a_id' => [
                'required',
                'string',
                'unique:cable,a_id',
                'unique:cable,b_id',
            ],
            'a_connector_id' => [
                'required',
                'integer',
                'exists:attributes_cable_connector,value',
            ],
            'b_id' => [
                'required',
                'string',
                'unique:cable,a_id',
                'unique:cable,b_id',
            ],
            'b_connector_id' => [
                'required',
                'integer',
                'exists:attributes_cable_connector,value',
            ],
            'media_id' => [
                'required',
                'integer',
                'exists:attributes_media,value',
            ],
            'length' => [
                'required',
                'integer',
            ],
        ];
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $data = $request->all();

        $aID = $data['a_id'];
        $aConnectorID = $data['a_connector_id'];
        $bID = $data['b_id'];
        $bConnectorID = $data['b_connector_id'];
        $mediaID = $data['media_id'];
        $length = $data['length'];

        // Validate cable connector compatiblity
        $errorArray = [];
        $aConnector = CableConnectorModel::where('value', $aConnectorID)->first();
        $bConnector = CableConnectorModel::where('value', $bConnectorID)->first();
        $media = MediaModel::where('value', $mediaID)->first();
        if($aConnector->type_id != $bConnector->type_id) {
            $errorArray['b_connector_id'] = 'Cable connectors are incompatible.';
        }
        if($aConnector->type_id != $media->type_id) {
            $errorArray['a_connector_id'] = 'Cable connector is incompatible with media. '.$aConnectorID.' - '.$aConnector->media_type_id.' - '.$media->type_id;
        }
        if($bConnector->type_id != $media->type_id) {
            $errorArray['b_connector_id'] = 'Cable connector is incompatible with media.';
        }
        if(count($errorArray)) {
            throw ValidationException::withMessages($errorArray);
        }

        $mediaType = MediaTypeModel::where('value', $media->type_id)->first();

        if($mediaType->name == 'Copper') {
            $cmLength = $PCM->converFeetToCm($length);
        } else {
            $cmLength = $PCM->converMetersToCm($length);
        }

        // Create new cable object
        $cable = new CableModel;

        $cable->a_id = $aID;
        $cable->a_connector_id = $aConnectorID;
        $cable->b_id = $bID;
        $cable->b_connector_id = $bConnectorID;
        $cable->media_id = $mediaID;
        $cable->length = $cmLength;

        // Save new cable object
        $cable->save();

        return $cable;
    }

    /**
     * Update resource.
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

        $PCM = new PCM;

        $request->request->add(['id' => $id]);
        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:cable,id'
            ]
        ];

        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Validate location is type cabinet
        $cable = CableModel::where('id', $request->id)->first();

        return $cable;

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
                'exists:cable'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();
				
		$cable = CableModel::where('id', $id)->first();

        $cable->delete();

        return array('id' => $id);
    }
}
