<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\TemplateModel;
use App\Models\ObjectsModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

class Objects extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $objects = ObjectsModel::all();

        return $objects;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Prepare variables
        $faceArray = [
            'front',
            'rear'
        ];

        // Validate Template ID
        $validatorInput = [
            'templateID' => $request->template_id,
        ];
        $validatorRules = [
            'templateID' => [
                'required',
                'exists:template,id'
            ]
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Retrieve object record
        $template = TemplateModel::where('id', $request->template_id)->first();
        $templateType = $template->type;

        if($templateType == 'standard') {

            $validatorInput = [
                'cabinetID' => $request->cabinet_id,
                'cabinetFace' => $request->cabinet_face,
                'cabinetRU' => $request->cabinet_ru,
                'templateFace' => $request->template_face
            ];
            $validatorRules = [
                'cabinetID' => [
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
        } else {

            $validatorInput = [
                'parentID' => $request->parent_id,
                'parentFace' => $request->parent_face,
                'parentPartitionAddress' => $request->parent_partition_address,
                'parentEnclosureAddress' => $request->parent_enclosure_address
            ];
            $validatorRules = [
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
        }
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        $object = new ObjectsModel;

        $object->name = "New_Object";
        $object->template_id = $request->template_id;

        if($templateType == 'standard') {
            $object->cabinet_id = $request->cabinet_id;
            $object->cabinet_ru = $request->cabinet_ru;
            $object->cabinet_front = ($request->cabinet_face == $request->template_face) ? 'front' : 'rear';
        } else {

            $parentObject = ObjectsModel::where('id', $request->parent_id)->first();
            $parentCabinetID = $parentObject->cabinet_id;

            $object->cabinet_id = $parentCabinetID;
            $object->parent_id = $request->parent_id;
            $object->parent_face = $request->parent_face;
            $object->parent_partition_address = $request->parent_partition_address;
            $object->parent_enclosure_address = $request->parent_enclosure_address;
        }

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
        // Validate template ID
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
            'cabinet_ru' => [
                'numeric',
                'unique:App\Models\TemplateModel,name',
                'between:1,52'
            ],
            'name' => [
                'alpha_dash',
                'min:1',
                'max:255'
            ],
        ]);

        // Store request data
        $data = $request->all();

        // Retrieve object record
        $object = ObjectsModel::where('id', $id)->first();

        // Update object record
        foreach($data as $key => $value) {
            if($key == 'cabinet_ru') {

                // Update object RU
                $object->cabinet_ru = $value;
            } else if($key == 'name') {

                // Update object name
                $object->name = $value;
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
				
		$object = ObjectsModel::where('id', $id)->first();

        $object->delete();

        return array('id' => $id);
    }
}
