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

        // Store request data
        $data = $request->all();

        $validatorInput = [
            'id' => $data['id'],
            'face' => $data['face'],
            'partition' => $data['partition'],
            'port-id' => $data['port_id'],
            'peer-data' => (isset($data['peer_data'])) ? $data['peer_data'] : array(),
            'cable-id' => (isset($data['cable_id'])) ? $data['cable_id'] : null,
        ];
        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:object'
            ],
            'face' => [
                'required',
                'in:front,rear'
            ],
            'partition' => [
                'required',
                'array'
            ],
            'port-id' => [
                'required',
                'numeric'
            ],
            'peer-data' => [
                'required_without:cable-id',
                'array',
                new ConnectionPeerData
            ],
            'cable-id' => [
                'required_without:peer-data',
                'string',
                'nullable',
                new CableID
            ],
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        if(isset($data['cable_id'])) {

            // Get cable
            $cable = CableModel::where('a_id', $data['cable_id'])
                ->orwhere('b_id', $data['cable_id'])
                ->first();

            // Validate cable
            if(!$cable) {
                throw ValidationException::withMessages(['cable_id' => 'Cable not found.']);
            }

            // Get remote cable ID
            $remoteSide = ($cable['a_id'] == $data['cable_id']) ? 'b' : 'a';
            $remoteCableID = $cable[$remoteSide.'_id'];

            // Get connection
            $remoteConnection = ConnectionModel::where('a_cable_id', $remoteCableID)
                ->orwhere('b_cable_id', $remoteCableID)
                ->first();

            // Generate peer data
            if($remoteConnection) {
                $localSide = ($remoteConnection['a_cable_id'] == $remoteCableID) ? 'a' : 'b';
                $remoteSide = ($remoteConnection['a_cable_id'] == $remoteCableID) ? 'b' : 'a';
                $peerData = array(array(
                    'id' => $remoteConnection[$localSide.'_id'],
                    'face' => $remoteConnection[$localSide.'_face'],
                    'partition' => $remoteConnection[$localSide.'_partition'],
                    'port_id' => $remoteConnection[$localSide.'_port'],
                    'cable_id' => $remoteCableID
                ));
            } else {
                $peerData = array(array(
                    'id' => null,
                    'face' => null,
                    'partition' => null,
                    'port_id' => null,
                    'cable_id' => null
                ));
            }
        } else {
            $peerData = $data['peer_data'];
        }

        // Ensure connection does not introduce loops
        $portA = array(
            'id' => $data['id'],
            'face' => $data['face'],
            'partition' => $data['partition'],
            'port_id' => $data['port_id'],
        );
        foreach($peerData as $key => $value) {
            $portB = array(
                'id' => $value['id'],
                'face' => $value['face'],
                'partition' => $value['partition'],
                'port_id' => $value['port_id'],
            );
            if (!$PCM->validateConnectionPath($portA, $portB)) {
                throw ValidationException::withMessages(['path_validation' => 'Loop detected.']);
            }
        }

        $returnData = array('add' => array(), 'remove' => array());

        // Find connections associated with selected object to be removed
        $filteredConnections = ConnectionModel::where(
            [
                ['a_id', $data['id']],
                ['a_face', $data['face']],
                ['a_partition', json_encode($data['partition'])],
                ['a_port', $data['port_id']]
            ]
        )->orwhere(
            [
                ['b_id', $data['id']],
                ['b_face', $data['face']],
                ['b_partition', json_encode($data['partition'])],
                ['b_port', $data['port_id']]
            ]
        )->get();
        $connectionDeleteArray = $filteredConnections->all();

        // Find connections associated with selected node(s) to be removed
        foreach($peerData as $key => $value) {

            $aAttributes = array(
                ['a_id', $value['id']],
                ['a_face', $value['face']],
                ['a_partition', json_encode($value['partition'])],
                ['a_port', json_encode($value['port_id'])]
            );
            $bAttributes = array(
                ['b_id', $value['id']],
                ['b_face', $value['face']],
                ['b_partition', json_encode($value['partition'])],
                ['b_port', json_encode($value['port_id'])]
            );

            $filteredConnections = ConnectionModel::where(
                $aAttributes
            )->orwhere(
                $bAttributes
            )->get();
            $connectionDeleteArray = array_merge($connectionDeleteArray, $filteredConnections->all());

        }

        // Delete any existing connection records
        foreach($connectionDeleteArray as $connectionDelete) {
            array_push($returnData['remove'], $connectionDelete);
            ConnectionModel::where('id', $connectionDelete['id'])->delete();
        }

        // Create new connection record(s)
        foreach($peerData as $key => $value) {

            // Create new connection object
            $connection = new ConnectionModel;

            // Store A side
            $connection->a_id = $data['id'];
            $connection->a_face = $data['face'];
            $connection->a_partition = $data['partition'];
            $connection->a_port = $data['port_id'];
            $connection->a_cable_id = (isset($data['cable_id'])) ? $data['cable_id'] : null;

            // Store B side
            $connection->b_id = $value['id'];
            $connection->b_face = $value['face'];
            $connection->b_partition = $value['partition'];
            $connection->b_port = $value['port_id'];
            $connection->b_cable_id = (isset($value['cable_id'])) ? $value['cable_id'] : null;

            

            // Save new connection object
            $connection->save();

            array_push($returnData['add'], $connection->toArray());
        }

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
