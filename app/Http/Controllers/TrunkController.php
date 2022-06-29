<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use App\Models\TemplateModel;
use App\Models\ObjectModel;
use App\Models\TrunkModel;
use App\Models\MediaModel;
use App\Models\PortConnectorModel;
use Illuminate\Support\Facades\Gate;

class TrunkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trunks = TrunkModel::all();

        return $trunks;
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

        // Validate
        $validatorInput = [
            'id' => $request->id,
            'face' => $request->face,
            'peer_data' => $request->peer_data,
        ];
        $validatorRules = [
            'id' => [
                'required',
                'numeric',
                'exists:object',
            ],
            'face' => [
                'required',
                'in:front,rear',
            ],
            'peer_data' => [
                'required',
                'array',
            ],
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Perform further validation
        $compatible = true;

        // Gather object data
        $objectID = $request->id;
        $objectFace = $request->face;
        $objectPartitionAddress = $request->partition;
        $object = ObjectModel::where('id', $objectID)->first();
        $floorplanType = $object->floorplan_object_type;
        if($floorplanType == null) {
            $objectTemplateID = $object->template_id;
            $objectTemplate = TemplateModel::where('id', $objectTemplateID)->first();
            $objectTemplateFunction = $objectTemplate->function;
            $objectBlueprint = $objectTemplate->blueprint;
            $objectPartition = $PCM->getPartition($objectBlueprint, $objectFace, $objectPartitionAddress);
            $objectPartitionMediaID = $objectPartition['media'];
            $objectPartitionPortConnectorID = $objectPartition['port_connector'];
            $objectPortTotal = $objectPartition['port_layout']['cols'] * $objectPartition['port_layout']['rows'];
        } else if($floorplanType == 'walljack') {
            $objectTemplateFunction = 'passive';
            $objectPartitionMediaID = 1;
            $objectPartitionPortConnectorID = 1;
            $objectPortTotal = 0;
        } else if($floorplanType == 'wap' || $floorplanType == 'camera') {
            $objectTemplateFunction = 'endpoint';
            $objectPartitionMediaID = 1;
            $objectPartitionPortConnectorID = 1;
            $objectPortTotal = 0;
        }
        $objectPartitionPortConnector = PortConnectorModel::where('value', $objectPartitionPortConnectorID)->first();
        $objectPartitionMediaTypeID = $objectPartitionPortConnector->type_id;

        // Loop through trunk peers
        foreach($request->peer_data as $peer) {

            // Gather peer data
            $peerID = $peer['id'];
            $peerFace = $peer['face'];
            $peerPartitionAddress = $peer['partition'];
            $peer = ObjectModel::where('id', $peerID)->first();
            $peerTemplateID = $peer->template_id;
            $peerTemplate = TemplateModel::where('id', $peerTemplateID)->first();
            $peerTemplateFunction = $peerTemplate->function;
            $peerBlueprint = $peerTemplate->blueprint;
            $peerPartition = $PCM->getPartition($peerBlueprint, $peerFace, $peerPartitionAddress);
            $peerPartitionMediaID = $peerPartition['media'];
            $peerPartitionPortConnectorID = $peerPartition['port_connector'];
            $peerPartitionPortConnector = PortConnectorModel::where('value', $peerPartitionPortConnectorID)->first();
            $peerPartitionMediaTypeID = $peerPartitionPortConnector->type_id;
            $peerPortTotal = $peerPartition['port_layout']['cols'] * $peerPartition['port_layout']['rows'];

            // Cannot trunk endpoint to endpoint
            if($objectTemplateFunction == 'endpoint' && $peerTemplateFunction == 'endpoint') {
                $compatible = false;
            }

            // Must have same number of ports
            if($objectPortTotal != $peerPortTotal && $objectPortTotal != 0) {
                $compatible = false;
            }

            // Media types must be compatible
            if($objectTemplateFunction == 'endpoint') {
                if($objectPartitionMediaTypeID != 4 && ($objectPartitionMediaTypeID != $peerPartitionMediaTypeID)) {
                    $compatible = false;
                }
            } else if($peerTemplateFunction == 'endpoint') {
                if($peerPartitionMediaTypeID != 4 && ($objectPartitionMediaTypeID != $objectPartitionMediaTypeID)) {
                    $compatible = false;
                }
            } else {
                $objectMedia = MediaModel::where('value', $objectPartitionMediaID)->first();
                $objectMediaCateogoryID = $objectMedia->category_id;
                $peerMedia = MediaModel::where('value', $peerPartitionMediaID)->first();
                $peerMediaCateogoryID = $peerMedia->category_id;
                if($objectMedia != $peerMedia) {
                    $compatible = false;
                }
            }
        }

        if(!$compatible) {
            throw ValidationException::withMessages(['peer_data' => 'Trunk peer is not compatible.']);
        }

        $returnData = array('add' => array(), 'remove' => array());

        // Store request data
        $data = $request->all();

        // Collect object
        $object = ObjectModel::where('id', $data['id'])->first();

        // Find trunks associated with selected object to be removed
        $filteredTrunks = TrunkModel::where(
            [
                ['a_id', $data['id']],
                ['a_face', $data['face']],
                ['a_partition', json_encode($data['partition'])]
            ]
        )->orwhere(
            [
                ['b_id', $data['id']],
                ['b_face', $data['face']],
                ['b_partition', json_encode($data['partition'])]
            ]
        )->get();
        $trunkDeleteArray = $filteredTrunks->all();

        // Find trunks associated with selected node(s) to be removed
        foreach($data['peer_data'] as $key => $value) {

            if($object['floorplan_object_type'] !== null) {
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
            } else {
                $aAttributes = array(
                    ['a_id', $value['id']],
                    ['a_face', $value['face']],
                    ['a_partition', json_encode($value['partition'])]
                );
                $bAttributes = array(
                    ['b_id', $value['id']],
                    ['b_face', $value['face']],
                    ['b_partition', json_encode($value['partition'])]
                );
            }

            $filteredTrunks = TrunkModel::where(
                $aAttributes
            )->orwhere(
                $bAttributes
            )->get();
            $trunkDeleteArray = array_merge($trunkDeleteArray, $filteredTrunks->all());

        }

        // Delete any existing trunk records
        foreach($trunkDeleteArray as $trunkDelete) {
            array_push($returnData['remove'], $trunkDelete);
            TrunkModel::where('id', $trunkDelete['id'])->delete();
        }

        // Create new trunk record(s)
        foreach($data['peer_data'] as $key => $value) {

            // Create new trunk object
            $trunk = new TrunkModel;

            // Store A side
            $trunk->a_id = $data['id'];
            $trunk->a_face = $data['face'];
            $trunk->a_partition = $data['partition'];
            $trunk->a_port = $data['port_id'];

            // Store B side
            $trunk->b_id = $value['id'];
            $trunk->b_face = $value['face'];
            $trunk->b_partition = $value['partition'];
            $trunk->b_port = $value['port_id'];

            // Save new trunk object
            $trunk->save();

            array_push($returnData['add'], $trunk->toArray());
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
                'exists:trunk'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();
				
		$trunk = TrunkModel::where('id', $id)->first();
        $trunk->delete();

        return array('id' => $id);
    }
}
