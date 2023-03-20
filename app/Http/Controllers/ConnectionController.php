<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use App\Models\ConnectionModel;
use App\Models\CableModel;
use App\Rules\ConnectionPeerData;
use App\Rules\CableID;
use Illuminate\Support\Facades\Gate;

class ConnectionController extends Controller
{

    public $archiveAddress = NULL;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $connections = ConnectionModel::all();

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

        $validatorRules = [
            'a_id' => [
                'required',
                'integer',
                'exists:object,id'
            ],
            'a_face' => [
                'required',
                'in:front,rear'
            ],
            'a_partition' => [
                'required',
                'array'
            ],
            'a_port' => [
                'required',
                'numeric'
            ],
            'a_cable_id' => [
                'sometimes',
                'string',
                'nullable',
                new CableID
            ],
            'b_id' => [
                'sometimes',
                'integer',
                'exists:object,id',
                'nullable',
            ],
            'b_face' => [
                'in:front,rear',
                'nullable',
            ],
            'b_partition' => [
                'array',
                'nullable',
            ],
            'b_port' => [
                'numeric',
                'nullable',
            ],
            'b_cable_id' => [
                'string',
                'nullable',
                new CableID
            ],
            'group_id' => [
                'sometimes',
                'integer',
            ],
        ];
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->sometimes(['b_face', 'b_partition', 'b_port'], 'required', function ($input) {
            if(isset($input->b_id)) {
                if($input->b_id != Null) {
                    return true;
                }
            }
            return false;
        });
        $customValidator->sometimes(['b_id', 'b_face', 'b_partition', 'b_port'], 'required', function ($input) {
            if(isset($input->b_cable_id)) {
                if($input->b_cable_id != Null) {
                    return true;
                }
            }
            return false;
        });
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $data = $request->all();

        // Remote port is not defined and must be found
        if(isset($data['a_cable_id']) and !isset($data['b_cable_id'])) {

            // Get cable
            $cable = CableModel::where('a_id', $data['a_cable_id'])
                ->orwhere('b_id', $data['a_cable_id'])
                ->first();

            // Get remote cable ID
            $remoteSide = ($cable['a_id'] == $data['a_cable_id']) ? 'b' : 'a';
            $remoteCableID = $cable[$remoteSide.'_id'];

            // Get connection
            $remoteConnection = ConnectionModel::where('a_cable_id', $remoteCableID)
                ->orwhere('b_cable_id', $remoteCableID)
                ->first();

            // Generate peer data
            if($remoteConnection) {
                $localSide = ($remoteConnection['a_cable_id'] == $remoteCableID) ? 'a' : 'b';

                $data['b_id'] = $remoteConnection[$localSide.'_id'];
                $data['b_face'] = $remoteConnection[$localSide.'_face'];
                $data['b_partition'] = $remoteConnection[$localSide.'_partition'];
                $data['b_port'] = $remoteConnection[$localSide.'_port'];
                $data['b_cable_id'] = $remoteCableID;
            } else {
                $data['b_id'] = NULL;
                $data['b_face'] = NULL;
                $data['b_partition'] = NULL;
                $data['b_port'] = NULL;
                $data['b_cable_id'] = NULL;
            }
        }

        // Fill in b_ side data if not set
        if(!isset($data['b_id'])) {
            $data['b_id'] = NULL;
            $data['b_face'] = NULL;
            $data['b_partition'] = NULL;
            $data['b_port'] = NULL;
            $data['b_cable_id'] = NULL;
        } else if($data['b_id'] == NULL) {
            $data['b_id'] = NULL;
            $data['b_face'] = NULL;
            $data['b_partition'] = NULL;
            $data['b_port'] = NULL;
            $data['b_cable_id'] = NULL;
        }

        // Ensure connection does not introduce loops
        $portA = array(
            'id' => $data['a_id'],
            'face' => $data['a_face'],
            'partition' => $data['a_partition'],
            'port_id' => $data['a_port'],
        );
        $portB = array(
            'id' => $data['b_id'],
            'face' => $data['b_face'],
            'partition' => $data['b_partition'],
            'port_id' => $data['b_port'],
        );
        if (!$PCM->validateConnectionPath($portA, $portB)) {
            throw ValidationException::withMessages(['path_validation' => 'Loop detected. '.$this->archiveAddress]);
        }

        $returnData = array('remove' => array());

        // Find connections associated with selected object to be removed
        $filteredConnections = ConnectionModel::where(
            [
                ['a_id', $data['a_id']],
                ['a_face', $data['a_face']],
                ['a_partition', json_encode($data['a_partition'])],
                ['a_port', $data['a_port']]
            ]
        )->orwhere(
            [
                ['b_id', $data['a_id']],
                ['b_face', $data['a_face']],
                ['b_partition', json_encode($data['a_partition'])],
                ['b_port', $data['a_port']]
            ]
        )->get()->toArray();
        $connectionDeleteArray = $filteredConnections;

        // Find connections associated with selected node(s) to be removed
        $filteredConnections = ConnectionModel::where(
            [
                ['a_id', $data['b_id']],
                ['a_face', $data['b_face']],
                ['a_partition', json_encode($data['b_partition'])],
                ['a_port', $data['b_port']]
            ]
        )->orwhere(
            [
                ['b_id', $data['b_id']],
                ['b_face', $data['b_face']],
                ['b_partition', json_encode($data['b_partition'])],
                ['b_port', $data['b_port']]
            ]
        )->get()->toArray();
        $connectionDeleteArray = array_merge($connectionDeleteArray, $filteredConnections);

        // Delete any existing connection records
        foreach($connectionDeleteArray as $connectionDelete) {
            array_push($returnData['remove'], $connectionDelete);
            ConnectionModel::where('id', $connectionDelete['id'])->delete();
        }

        // Create new connection object
        $connection = new ConnectionModel;

        // Store A side
        $connection->a_id = $data['a_id'];
        $connection->a_face = $data['a_face'];
        $connection->a_partition = $data['a_partition'];
        $connection->a_port = $data['a_port'];
        $connection->a_cable_id = (isset($data['a_cable_id'])) ? $data['a_cable_id'] : null;

        // Store B side
        $connection->b_id = $data['b_id'];
        $connection->b_face = $data['b_face'];
        $connection->b_partition = $data['b_partition'];
        $connection->b_port = $data['b_port'];
        $connection->b_cable_id = (isset($data['b_cable_id'])) ? $data['b_cable_id'] : null;

        // Save new connection object
        $connection->save();

        $returnData['add'] = $connection->toArray();

        return $returnData;
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
                'exists:connection'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();
				
		$object = ConnectionModel::where('id', $id)->first();

        $object->delete();

        return array('id' => $id);
    }
}
