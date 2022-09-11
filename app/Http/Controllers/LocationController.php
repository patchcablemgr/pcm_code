<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\LocationModel;
use App\Models\CablePathModel;
use App\Models\ObjectModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

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
     * Display a listing of the cable path resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCablePaths()
    {
        $cablePaths = CablePathModel::all();

        return $cablePaths;
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

        // Prepare variables
        $locationTypeArray = [
            'location',
            'pod',
            'cabinet',
            'floorplan'
        ];

        $validatorInput = [
            'parent-id' => $request->parent_id,
            'type' => $request->type,
        ];

        $validatorRules = [
            'parent-id' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'type' => [
                'required',
                Rule::in($locationTypeArray)
            ]
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $parentID = $request->parent_id;
        $locationType = $request->type;

        if($parentID !== 0) {

            $parent = LocationModel::where('id', $parentID)->first();
            $parentType = $parent['type'];

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
            if(!in_array($locationType, $typeCompatibility[$parentType])) {
                throw ValidationException::withMessages(['location-type' => 'Location type is incompatible with parent.']);
            }

        }

        $location = new LocationModel;

        $location->name = "New_".ucfirst($locationType);
        $location->parent_id = $parentID;
        $location->type = $locationType;
        $location->img = null;
        $location->size = ($locationType == 'cabinet') ? 42 : null;
        $location->ru_orientation = ($locationType == 'cabinet') ? 'bottom-up' : null;

        $location->save();

        $newLocation = LocationModel::where('id', $location->id)->first();
        return $newLocation->toArray();
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCablePath(Request $request, $id)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $validatorInput = [
            'id' => $id,
            'peer_id' => $request->peer_id,
            'distance' => $request->distance,
            'notes' => $request->notes,
        ];

        $validatorRules = [
            'id' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'peer_id' => [
                'required',
                'integer',
                'exists:location,id'
            ],
            'distance' => [
                'required',
                'integer',
                'between:1,1000'
            ],
            'notes' => [
                'string',
                'nullable',
                'max:255'
            ],
        ];

        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $cabinetAID = $id;
        $cabinetBID = $request->peer_id;
        $distance = $request->distance;
        $notes = ($request->notes) ? $request->notes : "";

        $cablePath = new CablePathModel;

        $cablePath->cabinet_a_id = $cabinetAID;
        $cablePath->cabinet_b_id = $cabinetBID;
        $cablePath->distance = $distance;
        $cablePath->notes = $notes;

        $cablePath->save();

        return $cablePath;
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
                'integer',
                'exists:location'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

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
                'exists:location'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Retrieve location record
        $location = LocationModel::where('id', $id)->first();
        $locationType = $location['type'];

        // Store request data
        $data = $request->all();

        // Update location record
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

                // Update location parent ID
                $location->parent_id = $value;
            }

            // Node order
            if($key == 'order') {

                // Update location name
                $location->order = $value;
            }

            // Location size
            if($key == 'size') {

                $validatorInput = [
                    'size' => $value
                ];
                $validatorRules = [
                    'size' => [
                        'integer',
                        'min:1',
                        'max:52'
                    ]
                ];
                $validatorMessages = [];
                Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

                // Validate location is cabinet
                $location = LocationModel::where('id', $id)->first();
                $locationType = $location['type'];
                if($locationType != 'cabinet') {
                    throw ValidationException::withMessages([$key => 'Location type is incompatible.']);
                }

                // Validate cabinet resize
                if($value < $location->size) {
                    $Err = $PCM->validateCabinetResize($id, $value);
                    if($Err !== true) {
                        throw ValidationException::withMessages($Err);
                    }
                }

                // Store original cabinet size
                $cabinetSize = $location->size;

                // Update cabinet size
                $location->size = intval($value);

                // Update object RU
                if($location->ru_orientation == 'bottom-up') {

                    $cabinetSizeDiff = ($cabinetSize > $value) ? (($cabinetSize - $value) * -1) : ($value - $cabinetSize);

                    $objects = ObjectModel::where('location_id', $id)->where('cabinet_ru', '<>', null)->get();
                    foreach($objects as $object) {
                        $object->cabinet_ru = $object->cabinet_ru + $cabinetSizeDiff;
                        $object->save();
                    }
                }
                
            }

            // Location ru orientation
            if($key == 'ru_orientation') {

                $validatorInput = [
                    'ru_orientation' => $value
                ];
                $validatorRules = [
                    'ru_orientation' => [
                        Rule::in(['bottom-up', 'top-down'])
                    ]
                ];
                $validatorMessages = [];
                Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

                $location = LocationModel::where('id', $id)->first();
                $locationType = $location['type'];
                if($locationType != 'cabinet') {
                    throw ValidationException::withMessages([$key => 'Location type is incompatible.']);
                }

                // Update cabinet size
                $location->ru_orientation = $value;
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
                'integer',
                'exists:location',
                'unique:object,location_id'
            ]
        ];
        $validatorMessages = [
            'id.unique' => 'The location contains objects and cannot be deleted.'
        ];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $location = LocationModel::where('id', $id)->first();

        $location->delete();

        return array('id' => $id);
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyCablePath($id)
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
                'integer',
                'exists:cable_path'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $cablePath = CablePathModel::where('id', $id)->first();

        $cablePath->delete();

        return array('id' => $id);
    }
}
