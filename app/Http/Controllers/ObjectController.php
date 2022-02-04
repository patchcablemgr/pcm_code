<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\TemplateModel;
use App\Models\ObjectModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

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

        $faceArray = [
            'front',
            'rear'
        ];

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
                'exists:template,id'
            ],
            'locationID' => [
                'required',
                'exists:location,id'
            ],
            'cabinetFace' => [
                'required',
                Rule::in($faceArray)
            ],
            'cabinetRU' => [
                'required',
                'integer'
            ],
            'templateFace' => [
                'required',
                Rule::in($faceArray)
            ],
        ];

        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        $object = new ObjectModel;

        $object->name = "New_Object";
        $object->template_id = $request->template_id;
        $object->location_id = $request->location_id;
        $object->cabinet_ru = $request->cabinet_ru;
        $object->cabinet_front = ($request->cabinet_face == $request->template_face) ? 'front' : 'rear';

        $object->save();

        return $object->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInsert(Request $request)
    {

        $faceArray = [
            'front',
            'rear'
        ];

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
                'exists:template,id'
            ],
            'parentID' => [
                'required',
                'exists:object,id'
            ],
            'parentFace' => [
                'required',
                Rule::in($faceArray)
            ],
            'parentPartitionAddress' => [
                'required'
            ],
            'parentEnclosureAddress' => [
                'required'
            ],
        ];

        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        $object = new ObjectModel;

        $parentObject = ObjectModel::where('id', $request->parent_id)->first();
        $parentCabinetID = $parentObject->location_id;

        $object->name = "New_Object";
        $object->template_id = $request->template_id;
        $object->location_id = $parentCabinetID;
        $object->parent_id = $request->parent_id;
        $object->parent_face = $request->parent_face;
        $object->parent_partition_address = $request->parent_partition_address;
        $object->parent_enclosure_address = $request->parent_enclosure_address;

        $object->save();

        return $object->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFloorplan(Request $request)
    {

        $floorplanObjectTypeArray = array(
            'device',
            'wap',
            'camera',
            'walljack'
        );

        $validatorInput = [
            'locationID' => $request->location_id,
            'floorplanObjectType' => $request->floorplan_object_type,
            'floorplanAddress' => $request->floorplan_address
        ];
        $validatorRules = [
            'locationID' => [
                'required',
                'exists:location,id'
            ],
            'floorplanObjectType' => [
                'required',
                Rule::in($floorplanObjectTypeArray)
            ],
            'floorplanAddress' => [
                'required'
            ]
        ];

        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        $object = new ObjectModel;

        $object->name = "New_Object";
        $object->location_id = $request->location_id;
        $object->floorplan_object_type = $request->floorplan_object_type;
        $object->floorplan_address = $request->floorplan_address;

        $object->save();

        return $object->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        // Prepare variables
        $faceArray = [
            'front',
            'rear'
        ];

        // Validate object ID
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:object'
            ]
        ];
        $validatorMessages = [
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Validate request data
        $request->validate([
            'name' => [
                'regex:/^[A-Za-z0-9\/]+$/',
                'min:1',
                'max:255'
            ],
        ]);

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

        } else if($objectType == 'insert') {
            
            // Validate insert data
            $request->validate([
                'parent_id' => [
                    'numeric',
                    'exists:object,id'
                ],'parent_face' => [
                    Rule::in($faceArray)
                ],'parent_partition_address' => [
                    'array'
                ],'parent_enclosure_address' => [
                    'array'
                ],
            ]);
        } else if($objectType == 'standard') {

            // Validate standard data
            $request->validate([
                'cabinet_ru' => [
                    'numeric',
                    'unique:App\Models\TemplateModel,name',
                    'between:1,52'
                ],
            ]);
        }

        

        // ToDo
        // Deep validation of:
        //  cabinet_ru,
        //  parent_id,
        //  parent_face,
        //  parent_partition_address,
        //  parent_enclosure_address

        // Store request data
        $data = $request->all();

        // Update object record
        foreach($data as $key => $value) {
            if($key == 'cabinet_ru') {

                // Update object RU
                $object->cabinet_ru = $value;
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
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:object'
            ]
        ];
        $validatorMessages = [
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();
				
		$object = ObjectModel::where('id', $id)->first();

        $object->delete();

        return array('id' => $id);
    }
}
