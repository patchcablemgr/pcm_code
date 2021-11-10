<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\LocationsModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

class Locations extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = LocationsModel::all();

        return $locations;
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
        $locationTypeArray = [
            'location',
            'pod',
            'cabinet',
            'floorplan'
        ];

        $request->validate([
            'parent_id' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'type' => [
                'required',
                Rule::in($locationTypeArray)
            ]
        ]);

        $location = new LocationsModel;

        $location->name = "New_".ucfirst($request->type);
        $location->parent_id = $request->parent_id;
        $location->type = $request->type;
        $location->img = null;
        $location->size = ($request->type == 'cabinet') ? 42 : null;

        $location->save();

        return $location->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:location'
            ]
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Retrieve location record
        $location = LocationsModel::where('id', $id)->first();

        return $location;
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
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'integer',
                'required',
                'exists:location'
            ]
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Store request data
        $data = $request->all();

        // Retrieve location record
        $location = LocationsModel::where('id', $id)->first();

        // Update template record
        foreach($data as $key => $value) {

            // Node text
            if($key == 'text') {

                // Update location name
                $location->name = $value;
            }

            // Node parent
            if($key == 'parent') {

                // Update location name
                $location->parent_id = $value;
            }
        }

        // Save template record
        $location->save();

        // Return template record
        return $location->toArray();
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
                'exists:location',
                'unique:object,location_id'
            ]
        ];
        $validatorMessages = [
            'id.unique' => 'The location contains objects and cannot be deleted.'
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        $location = LocationsModel::where('id', $id)->first();

        $location->delete();

        return array('id' => $id);
    }
}
