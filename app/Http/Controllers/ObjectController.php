<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\TemplateModel;
use App\Models\ObjectModel;
use App\Models\LocationModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class ObjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $objects = ObjectModel::all();

        return $objects;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStandard(Request $request)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $PCM = new PCM;

        $validatorInput = [
            'templateID' => $request->template_id,
            'locationID' => $request->location_id,
            'cabinetFace' => $request->cabinet_face,
            'cabinetRU' => $request->cabinet_ru,
            'templateFace' => $request->template_face
        ];
        $validatorRules = [
            'templateID' => [
                'required',
                'integer',
                'exists:template,id'
            ],
            'locationID' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'cabinetFace' => [
                'required',
                'in:front,rear',
            ],
            'cabinetRU' => [
                'required',
                'integer'
            ],
            'templateFace' => [
                'required',
                'in:front,rear',
            ],
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $cabinet = LocationModel::where('id', $request->location_id)->first();

        // Store new object variables
        $objectName = "New_Object";
        $templateID = $request->template_id;
        $locationID = $request->location_id;
        $cabinetRU = $PCM->getRUIndex($request->cabinet_ru, $cabinet->size, $cabinet->ru_orientation);
        $cabinetFace = $request->cabinet_face;
        $templateFace = $request->template_face;
        $cabinetFront = ($cabinetFace == $templateFace) ? 'front' : 'rear';

        // Validate RU occupancy
        $Err = $PCM->validateRUOccupancy($locationID, $templateID, $cabinetRU, $cabinetFace);
        if($Err !== true) {
            throw ValidationException::withMessages($Err);
        }

        $object = new ObjectModel;

        $object->name = $objectName;
        $object->template_id = $templateID;
        $object->location_id = $locationID;
        $object->cabinet_ru = $cabinetRU;
        $object->cabinet_front = $cabinetFront;

        $object->save();

        $newObject = ObjectModel::where('id', $object->id)->first();
        return $newObject->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInsert(Request $request)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $PCM = new PCM;

        $validatorInput = [
            'templateID' => $request->template_id,
            'parentID' => $request->parent_id,
            'parentFace' => $request->parent_face,
            'parentPartitionAddress' => $request->parent_partition_address,
            'parentEnclosureAddress' => $request->parent_enclosure_address
        ];
        $validatorRules = [
            'templateID' => [
                'required',
                'integer',
                'exists:template,id'
            ],
            'parentID' => [
                'required',
                'integer',
                'exists:object,id'
            ],
            'parentFace' => [
                'required',
                'in:front,rear',
            ],
            'parentPartitionAddress' => [
                'array',
                'required'
            ],
            'parentEnclosureAddress' => [
                'array',
                'required'
            ],
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Store new object variables
        $objectName = "New_Object";
        $objectTemplateID = $request->template_id;
        $objectParentID = $request->parent_id;
        $objectParentFace = $request->parent_face;
        $objectParentPartitionAddress = $request->parent_partition_address;
        $objectParentEnclosureAddress = $request->parent_enclosure_address;
        $parentObject = ObjectModel::where('id', $objectParentID)->first();
        $parentObjectTemplateID = $parentObject['template_id'];
        $objectLocationID = $parentObject->location_id;

        // Validate parent object partition
        $Err = $PCM->validateParentPartition($parentObjectTemplateID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress);
        if($Err !== true) {
            throw ValidationException::withMessages($Err);
        }

        // Validate parent object enclosure occupancy
        $Err = $PCM->validateEnclosureOccupancy($objectParentID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress);
        if($Err !== true) {
            throw ValidationException::withMessages($Err);
        }

        $object = new ObjectModel;

        $object->name = $objectName;
        $object->template_id = $objectTemplateID;
        $object->location_id = $objectLocationID;
        $object->parent_id = $objectParentID;
        $object->parent_face = $objectParentFace;
        $object->parent_partition_address = $objectParentPartitionAddress;
        $object->parent_enclosure_address = $objectParentEnclosureAddress;

        $object->save();

        $newObject = ObjectModel::where('id', $object->id)->first();
        return $newObject->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFloorplan(Request $request)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $validatorInput = [
            'locationID' => $request->location_id,
            'floorplanObjectType' => $request->floorplan_object_type,
            'floorplanAddress' => $request->floorplan_address
        ];
        $validatorRules = [
            'locationID' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'floorplanObjectType' => [
                'required',
                'in:device,wap,camera,walljack',
            ],
            'floorplanAddress' => [
                'required',
                'array',
            ]
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $location = LocationModel::where('id', $request->location_id)->first();
        if($location['type'] != 'floorplan') {
            throw ValidationException::withMessages(['location_id' => 'Object is not compatible with location type.']);
        }

        $object = new ObjectModel;

        $object->name = "New_Object";
        $object->location_id = $request->location_id;
        $object->floorplan_object_type = $request->floorplan_object_type;
        $object->floorplan_address = $request->floorplan_address;

        $object->save();

        return $object->toArray();
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
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $PCM = new PCM;

        // Validate object ID
        $validatorInput = [
            'id' => $id,
        ];
        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:object',
            ],
        ];
        if($request->name) {
            $validatorInput = array_merge($validatorInput, array(
                'name' => $request->name
            ));
            $validatorRules = array_merge($validatorRules, array(
                'name' => [
                    'regex:/^[A-Za-z0-9\/\_]+$/',
                    'min:1',
                    'max:255'
                ]
            ));
        }
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Retrieve object record
        $object = ObjectModel::where('id', $id)->first();

        // Determine object type
        if($object['floorplan_object_type'] !== null) {
            $objectType = 'floorplan';
        } else if($object['parent_id'] !== null) {
            $objectType = 'insert';
        } else {
            $objectType = 'standard';
        }

        if($objectType == 'floorplan') {

            // Validate insert data
            $request->validate([
                'floorplan_address' => [
                    'array',
                ]
            ]);

        } else if($objectType == 'insert') {
            // Validate insert data

            if($request->cabinet_ru) {
            
                // Validate parent data
                $request->validate([
                    'parent_id' => [
                        'required',
                        'integer',
                        'exists:object,id'
                    ],'parent_face' => [
                        'required',
                        'in:front,rear',
                    ],'parent_partition_address' => [
                        'required',
                        'array'
                    ],'parent_enclosure_address' => [
                        'required',
                        'array'
                    ],
                ]);

                // Collect request variables required for validation
                $objectParentID = $request->parent_id;
                $objectParentFace = $request->parent_face;
                $objectParentPartitionAddress = $request->parent_partition_address;
                $objectParentEnclosureAddress = $request->parent_enclosure_address;
                $parentObject = ObjectModel::where('id', $objectParentID)->first();
                $parentObjectTemplateID = $parentObject['template_id'];

                // Validate parent object partition
                $Err = $PCM->validateParentPartition($parentObjectTemplateID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress);
                if($Err !== true) {
                    throw ValidationException::withMessages($Err);
                }

                // Validate parent object enclosure occupancy
                $Err = $PCM->validateEnclosureOccupancy($objectParentID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress);
                if($Err !== true) {
                    throw ValidationException::withMessages($Err);
                }
            }

        } else if($objectType == 'standard') {
            // Validate standard data

            if($request->cabinet_ru) {

                // Validate cabinet RU
                $request->validate([
                    'cabinet_ru' => [
                        'integer',
                        'between:1,52'
                    ],
                ]);

                

                // Collect request variables required for validation
                $templateID = $object->template_id;
                $locationID = $object->location_id;
                $cabinet = LocationModel::where('id', $locationID)->first();
                $cabinetRU = $PCM->getRUIndex($request->cabinet_ru, $cabinet->size, $cabinet->ru_orientation);
                $cabinetFace = $request->cabinet_face;

                // Validate RU occupancy
                if($object['cabinet_ru'] != $cabinetRU) {
                    $Err = $PCM->validateRUOccupancy($locationID, $templateID, $cabinetRU, $cabinetFace);
                    if($Err !== true) {
                        throw ValidationException::withMessages($Err);
                    }
                }
            }
        }

        // Store request data
        $data = $request->all();

        // Update object record
        foreach($data as $key => $value) {
            if($key == 'cabinet_ru') {

                $cabinet = LocationModel::where('id', $request->location_id)->first();

                // Update object RU
                $object->cabinet_ru = $PCM->getRUIndex($value, $cabinet->size, $cabinet->ru_orientation);
            } else if($key == 'name') {

                // Update object name
                $object->name = $value;
            } else if($key == 'parent_id') {

                // Update parent ID
                $object->parent_id = $value;
            } else if($key == 'parent_face') {

                // Update parent face
                $object->parent_face = $value;
            } else if($key == 'parent_partition_address') {

                // Update parent partition address
                $object->parent_partition_address = $value;
            } else if($key == 'parent_enclosure_address') {

                // Update parent enclosure address
                $object->parent_enclosure_address = $value;
            } else if($key == 'floorplan_address') {

                // Update parent enclosure address
                $object->floorplan_address = $value;
            }
        }

        // Save object record
        $object->save();

        // Return object record
        return $object->toArray();
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

        $PCM = new PCM;

        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:object'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();
				
		$PCM->deleteObject($id);

        return array('id' => $id);
    }
}
