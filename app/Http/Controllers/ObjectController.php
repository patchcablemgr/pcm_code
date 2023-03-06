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
use App\Rules\RUOccupancy;

class ObjectController extends Controller
{

    public $archiveAddress = NULL;
    
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
        Log::info($request);

        $validatorRules = [
            'template_id' => [
                'required',
                'integer',
                'exists:template,id'
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'cabinet_front' => [
                'required',
                'in:front,rear',
            ],
            'cabinet_ru' => [
                'required',
                'integer',
                'between:1,52',
                new RUOccupancy(null, $request->location_id, $request->template_id, $request->cabinet_ru, $request->cabinet_face, $this->archiveAddress)
            ]
        ];

        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $cabinet = LocationModel::where('id', $request->location_id)->first();

        // Store new object variables
        $objectName = "New_Object";
        $templateID = $request->template_id;
        $locationID = $request->location_id;
        $cabinetRU = $PCM->getRUIndex($request->cabinet_ru, $cabinet->size, $cabinet->ru_orientation);
        $cabinetFront = $request->cabinet_front;

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

        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
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

        $PCM = new PCM;

        $validatorRules = [
            'location_id' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'floorplan_object_type' => [
                'required',
                'in:device,wap,camera,walljack',
            ],
            'floorplan_address' => [
                'required',
                'array',
            ]
        ];

        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
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

        $request->request->add(['id' => $id]);
        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:object',
            ],
            'name' => [
                'sometimes',
                'regex:/^[A-Za-z0-9\/\_]+$/',
                'min:1',
                'max:255'
            ]
        ];
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
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

        // Retrieve object parent data
        $objectParentID = $object['parent_id'];
        $objectParentFace = $object['parent_face'];
        $objectParentPartitionAddress = $object['parent_partition_address'];
        $objectParentEnclosureAddress = $object['parent_enclosure_address'];

        if($objectType == 'floorplan') {

            // Validate floorplan data

            $validatorRules = [
                'floorplan_address' => [
                    'sometimes',
                    'array',
                ]
            ];
            $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
            $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
            $customValidator->stopOnFirstFailure();
            $customValidator->validate();

        } else if($objectType == 'insert') {
            // Validate insert data

            $validatorRules = [
                'parent_id' => [
                    'sometimes',
                    'integer',
                    'exists:object,id'
                ],'parent_face' => [
                    'required_with:parent_id',
                    'in:front,rear',
                ],'parent_partition_address' => [
                    'required_with:parent_id',
                    'array'
                ],'parent_enclosure_address' => [
                    'required_with:parent_id',
                    'array'
                ],
            ];
            $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
            $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
            $customValidator->stopOnFirstFailure();
            $customValidator->validate();

            // Collect request variables required for validation
            $parentObject = ObjectModel::where('id', $objectParentID)->first();
            $parentObjectTemplateID = $parentObject['template_id'];

            // Validate parent object partition
            $Err = $PCM->validateParentPartition($parentObjectTemplateID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress);
            if($Err !== true) {
                throw ValidationException::withMessages($Err);
            }

            // Validate parent object enclosure occupancy
            $Err = $PCM->validateEnclosureOccupancy($id, $objectParentID, $objectParentFace, $objectParentPartitionAddress, $objectParentEnclosureAddress);
            if($Err !== true) {
                throw ValidationException::withMessages($Err);
            }

        } else if($objectType == 'standard') {
            // Validate standard data

            $validatorRules = [
                'cabinet_ru' => [
                    'sometimes',
                    'integer',
                    'between:1,52',
                    new RUOccupancy($request->id, $request->location_id, $request->template_id, $request->cabinet_ru, $request->cabinet_face, $this->archiveAddress)
                ],
            ];
            $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
            $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
            $customValidator->stopOnFirstFailure();
            $customValidator->validate();

            if($request->cabinet_ru) {

                // Collect request variables required for validation
                $templateID = $object->template_id;
                $locationID = $object->location_id;
                $cabinet = LocationModel::where('id', $locationID)->first();
                $cabinetRU = $PCM->getRUIndex($request->cabinet_ru, $cabinet->size, $cabinet->ru_orientation);
                $cabinetFace = $request->cabinet_face;

            }
        }

        // Store request data
        $data = $request->all();

        // Update object record
        foreach($data as $key => $value) {

            if($key == 'name') {

                // Update object name
                $object->name = $value;
            }

            if($objectType == 'floorplan') {

                if($key == 'floorplan_address') {

                    // Update parent enclosure address
                    $object->floorplan_address = $value;
                }

            } else if($objectType == 'insert') {

                if($key == 'parent_id') {

                    // Update parent ID
                    $object->parent_id = $value;
                } else if(isset($data['parent_id']) && $key == 'parent_face') {
    
                    // Update parent face
                    $object->parent_face = $value;
                } else if(isset($data['parent_id']) && $key == 'parent_partition_address') {
    
                    // Update parent partition address
                    $object->parent_partition_address = $value;
                } else if(isset($data['parent_id']) && $key == 'parent_enclosure_address') {
    
                    // Update parent enclosure address
                    $object->parent_enclosure_address = $value;
                }

            } else if($objectType == 'standard') {

                if($key == 'cabinet_ru') {

                    $cabinet = LocationModel::where('id', $request->location_id)->first();
    
                    // Update object RU
                    $object->cabinet_ru = $PCM->getRUIndex($value, $cabinet->size, $cabinet->ru_orientation);
                }
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
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();
				
		$PCM->deleteObject($id);

        return array('id' => $id);
    }
}
