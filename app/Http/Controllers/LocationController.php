<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\LocationModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = LocationModel::all();

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

        $location = new LocationModel;

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
        $location = LocationModel::where('id', $id)->first();

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

        // Retrieve location record
        $location = LocationModel::where('id', $id)->first();
        $locationType = $location['type'];



        // Store request data
        $data = $request->all();

        

        // Update template record
        foreach($data as $key => $value) {

            // Node text
            if($key == 'name') {

                $validatorInput = [
                    'name' => $value
                ];
                $validatorRules = [
                    'name' => [
                        'alpha_dash',
                        'min:1',
                        'max:255'
                    ]
                ];
                $validatorMessages = [];
                Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

                // Update location name
                $location->name = $value;
            }

            // Node parent
            if($key == 'parent_id') {

                if($value !== 0) {
                    $validatorInput = [
                        'parent_id' => $value,
                    ];
                    $validatorRules = [
                        'parent_id' => [
                            'integer',
                            'exists:location,id'
                        ]
                    ];
                    $validatorMessages = [];
                    Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

                    $parentLocation = LocationModel::where('id', $value)->first();
                    $parentLocationType = $parentLocation['type'];

                    $typeCompatibility = array(
                        'location' => array(
                            'location',
                            'pod',
                            'cabinet',
                            'floorplan'
                        ),
                        'pod' => array(
                            'cabinet'
                        ),
                        'floorplan' => array(),
                        'cabinet' => array()
                    );
                    if(!in_array($locationType, $typeCompatibility[$parentLocationType])) {
                        throw ValidationException::withMessages([$key => 'Parent type is incompatible.']);
                    }

                }

                // Update location name
                $location->parent_id = $value;
            }

            // Node order
            if($key == 'order') {

                // Update location name
                $location->order = $value;
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

        $location = LocationModel::where('id', $id)->first();

        $location->delete();

        return array('id' => $id);
    }
}
