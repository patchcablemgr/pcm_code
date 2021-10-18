<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        //
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

        $validatorInput = [
            'cabinetID' => $request->cabinet_id,
            'cabinetFace' => $request->cabinet_face,
            'cabinetRU' => $request->cabinet_ru,
            'templateID' => $request->template_id,
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
            'templateID' => [
                'required',
                'exists:template,id'
            ],
            'templateFace' => [
                'required',
                Rule::in($faceArray)
            ]
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        $object = new ObjectsModel;

        $object->name = "New_Object";
        $object->cabinet_id = $request->cabinet_id;
        $object->template_id = $request->template_id;
        $object->cabinet_ru = $request->cabinet_ru;
        $object->cabinet_front = ($request->cabinet_face == $request->template_face) ? 'front' : 'rear';

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
