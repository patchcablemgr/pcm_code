<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Models\PortModel;

class PortController extends Controller
{

    public $archiveAddress = NULL;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ports = PortModel::all();

        return $ports;
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
            'object_id' => [
                'required',
                'exists:object,id',
            ],
            'object_face' => [
                'required',
                'in:front,rear'
            ],
            'object_partition' => [
                'required',
                'array'
            ],
            'port_id' => [
                'required',
                'numeric'
            ],
            'description' => [
                'required',
                'string',
                'nullable'
            ],
        ];
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $data = $request->all();

        // Find connections associated with selected object to be removed
        $port = portModel::where('object_id', $data['object_id'])
            ->where('object_face', $data['object_face'])
            ->where('object_partition', json_encode($data['object_partition']))
            ->where('port_id', $data['port_id'])
            ->first();

        if(!$port) {

            // Create new port
            $port = new portModel;

        }

        // Store data
        $port->object_id = $data['object_id'];
        $port->object_face = $data['object_face'];
        $port->object_partition = $data['object_partition'];
        $port->port_id = $data['port_id'];
        $port->description = $data['description'];
        $port->save();

        return $port;
    }
}
