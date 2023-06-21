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

    public $archiveAddress = NULL;
    
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

        $validatorRules = [
            'a_id' => [
                'required',
                'numeric',
                'exists:object,id',
            ],
            'a_face' => [
                'required',
                'in:front,rear',
            ],
            'a_port' => [
                'sometimes',
                'numeric',
            ],
            'b_id' => [
                'required',
                'numeric',
                'exists:object,id',
            ],
            'b_face' => [
                'required',
                'in:front,rear',
            ],
            'b_port' => [
                'sometimes',
                'numeric',
                'nullable'
            ],
            'group_id' => [
                'sometimes',
                'numeric',
                'nullable'
            ],
        ];

        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Perform further validation
        $compatible = true;

        // Validate that both objects are not floorplan objects
        $aObjectID = $request->a_id;
        $aObjectFace = $request->a_face;
        $aObjectPartitionAddress = $request->a_partition;
        $aObject = ObjectModel::where('id', $aObjectID)->first();
        $aFloorplanObjectType = $aObject->floorplan_object_type;

        $bObjectID = $request->b_id;
        $bObjectFace = $request->b_face;
        $bObjectPartitionAddress = $request->b_partition;
        $bObject = ObjectModel::where('id', $bObjectID)->first();
        $bFloorplanObjectType = $bObject->floorplan_object_type;

        if(!is_null($aFloorplanObjectType) & !is_null($bFloorplanObjectType)) {
            throw ValidationException::withMessages(['peer_data' => 'Trunk peer is not compatible. '.$this->archiveAddress]);
        }

        if($bFloorplanObjectType) {
            $objectID = $bObjectID;
            $objectFace = $bObjectFace;
            $objectPartitionAddress = $bObjectPartitionAddress;
            $object = $bObject;

            $peerID = $aObjectID;
            $peerFace = $aObjectFace;
            $peerPartitionAddress = $aObjectPartitionAddress;
            $peer = $aObject;
        } else {
            $objectID = $aObjectID;
            $objectFace = $aObjectFace;
            $objectPartitionAddress = $aObjectPartitionAddress;
            $object = $aObject;

            $peerID = $bObjectID;
            $peerFace = $bObjectFace;
            $peerPartitionAddress = $bObjectPartitionAddress;
            $peer = $bObject;
        }

        // Gather object data
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

        // Gather peer data
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
            if($objectMedia['category_id'] != $peerMedia['category_id']) {
                $compatible = false;
            }
        }

        if(!$compatible) {
            throw ValidationException::withMessages(['peer_data' => 'Trunk peer is not compatible. '.$this->archiveAddress]);
        }

        $returnData = array('remove' => array());

        // Store request data
        $data = $request->all();

        // Collect object
        $object = ObjectModel::where('id', $data['a_id'])->first();

        // Gather data
        $aID = $data['a_id'];
        $aFace = $data['a_face'];
        $aPartition = json_encode($data['a_partition']);
        $aPort = $data['a_port'];
        $bID = $data['b_id'];
        $bFace = $data['b_face'];
        $bPartition = json_encode($data['b_partition']);
        $bPort = $data['b_port'];
        $groupID = $data['group_id'];

        // Find trunks associated with selected object to be removed
        $filteredTrunks = TrunkModel::where(function ($query) use ($aID, $aFace, $aPartition) {
            $query->where([
                ['a_id', $aID],
                ['a_face', $aFace],
                ['a_partition', $aPartition]
            ])
            ->orWhere([
                ['b_id', $aID],
                ['b_face', $aFace],
                ['b_partition', $aPartition]
            ]);
        })->where(function ($query) use ($groupID) {
            $query->where('group_id', '<>', $groupID);
        })->get();
        $trunkDeleteArray = $filteredTrunks->all();

        // Find trunks associated with selected node(s) to be removed
        if($object['floorplan_object_type'] !== null) {
            $filteredTrunks = TrunkModel::where(function ($query) use ($bID, $bFace, $bPartition, $bPort) {
                $query->where([
                    ['a_id', $bID],
                    ['a_face', $bFace],
                    ['a_partition', $bPartition],
                    ['a_port', $bPort]
                ])
                ->orWhere([
                    ['a_id', $bID],
                    ['a_face', $bFace],
                    ['a_partition', $bPartition],
                    ['a_port', null]
                ])->orWhere([
                    ['b_id', $bID],
                    ['b_face', $bFace],
                    ['b_partition', $bPartition],
                    ['b_port', $bPort]
                ])->orWhere([
                    ['b_id', $bID],
                    ['b_face', $bFace],
                    ['b_partition', $bPartition],
                    ['b_port', null]
                ]);
            })->where(function ($query) use ($groupID) {
                $query->where('group_id', '<>', $groupID);
            })->get();
        } else {
            $filteredTrunks = TrunkModel::where(function ($query) use ($bID, $bFace, $bPartition, $bPort) {
                $query->where([
                    ['a_id', $bID],
                    ['a_face', $bFace],
                    ['a_partition', $bPartition]
                ])
                ->orWhere([
                    ['b_id', $bID],
                    ['b_face', $bFace],
                    ['b_partition', $bPartition]
                ]);
            })->where(function ($query) use ($groupID) {
                $query->where('group_id', '<>', $groupID);
            })->get();
        }
        $trunkDeleteArray = array_merge($trunkDeleteArray, $filteredTrunks->all());

        // Delete any existing trunk records
        foreach($trunkDeleteArray as $trunkDelete) {
            array_push($returnData['remove'], $trunkDelete);
            TrunkModel::where('id', $trunkDelete['id'])->delete();
        }

        // Create new trunk object
        $trunk = new TrunkModel;

        // Store A side
        $trunk->a_id = $data['a_id'];
        $trunk->a_face = $data['a_face'];
        $trunk->a_partition = $data['a_partition'];
        $trunk->a_port = ($floorplanType == null) ? null : $data['a_port'];

        // Store B side
        $trunk->b_id = $data['b_id'];
        $trunk->b_face = $data['b_face'];
        $trunk->b_partition = $data['b_partition'];
        $trunk->b_port = ($floorplanType == null) ? null : $data['b_port'];

        // Store group ID
        $trunk->group_id = ($data['group_id']) ? $data['group_id'] : null;

        

        // Save new trunk object
        $trunk->save();

        $returnData['add'] = $trunk->toArray();

        return $returnData;
    }

    /**
     * Update the specified resource.
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
                'exists:trunk'
            ]
        ];
        
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $trunk = TrunkModel::where('id', $id)->first();

        return $trunk;

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
