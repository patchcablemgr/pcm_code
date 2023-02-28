<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\LocationModel;
use App\Models\TemplateModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class ImageController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeLocationImage(Request $request, $id)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        Log::info($request);
        Log::info($request->file('file')->getMimeType());

        // Prepare variables
        $validatorInput = [
            'id' => $id,
            'file' => $request->file
        ];
        $validatorRules = [
            'id' => [
                'integer',
                'exists:location,id'
            ],
            'file' => [
                'mimes:jpg,png,gif',
                'max:2048'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Retrieve location record
        $location = LocationModel::where('id', $id)->first();

        // Store floorplan image
        $path = $request->file('file')->store('images');

        // Update location floorplan image
        $pathArray = explode('/', $path);
        $location->img = end($pathArray);
            
        // Save location record
        $location->save();

        // Return location record
        return $location->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeTemplateImage(Request $request, $id)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        // Prepare variables
        $validatorInput = [
            'id' => $id,
            'file' => $request->file,
            'face' => $request->face
        ];
        $validatorRules = [
            'id' => [
                'integer',
                'exists:template,id'
            ],
            'file' => [
                'mimes:jpg,jpeg,png,gif',
                'max:512'
            ],
            'face' => [
                'in:front,rear'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Retrieve record
        $template = TemplateModel::where('id', $id)->first();

        // Store image
        $path = $request->file('file')->store('images');

        // Update template image
        $pathArray = explode('/', $path);
        if($request->face == 'front') {
            $template->img_front = end($pathArray);
        } else {
            $template->img_rear = end($pathArray);
        }
            
        // Save record
        $template->save();

        // Return record
        return $template->toArray();
    }
}
