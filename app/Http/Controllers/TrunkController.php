<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\TemplateModel;
use App\Models\ObjectModel;
use App\Models\TrunkModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

class TrunkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trunks = TrunkModel::all();

        return $trunks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $returnData = array('add' => array(), 'remove' => array());

        // Store request data
        $data = $request->all();

        // Collect object
        $object = ObjectModel::where('id', $data['id'])->first();

        // Find trunks associated with selected object to be removed
        $filteredTrunks = TrunkModel::where(
            [
                ['a_id', $data['id']],
                ['a_face', $data['face']],
                ['a_partition', json_encode($data['partition'])]
            ]
        )->orwhere(
            [
                ['b_id', $data['id']],
                ['b_face', $data['face']],
                ['b_partition', json_encode($data['partition'])]
            ]
        )->get();
        $trunkDeleteArray = $filteredTrunks->all();

        // Find trunks associated with selected node(s) to be removed
        foreach($data['PeerData'] as $key => $value) {

            if($object['floorplan_object_type'] !== null) {
                $aAttributes = array(
                    ['a_id', $value['id']],
                    ['a_face', $value['face']],
                    ['a_partition', json_encode($value['partition'])],
                    ['a_port', json_encode($value['port_id'])]
                );
                $bAttributes = array(
                    ['b_id', $value['id']],
                    ['b_face', $value['face']],
                    ['b_partition', json_encode($value['partition'])],
                    ['b_port', json_encode($value['port_id'])]
                );
            } else {
                $aAttributes = array(
                    ['a_id', $value['id']],
                    ['a_face', $value['face']],
                    ['a_partition', json_encode($value['partition'])]
                );
                $bAttributes = array(
                    ['b_id', $value['id']],
                    ['b_face', $value['face']],
                    ['b_partition', json_encode($value['partition'])]
                );
            }

            $filteredTrunks = TrunkModel::where(
                $aAttributes
            )->orwhere(
                $bAttributes
            )->get();
            $trunkDeleteArray = array_merge($trunkDeleteArray, $filteredTrunks->all());

        }

        // Delete any existing trunk records
        foreach($trunkDeleteArray as $trunkDelete) {
            array_push($returnData['remove'], $trunkDelete);
            TrunkModel::where('id', $trunkDelete['id'])->delete();
        }

        // Create new trunk record(s)
        foreach($data['PeerData'] as $key => $value) {

            // Create new trunk object
            $trunk = new TrunkModel;

            // Store A side
            $trunk->a_id = $data['id'];
            $trunk->a_face = $data['face'];
            $trunk->a_partition = $data['partition'];
            $trunk->a_port = $data['port_id'];

            // Store B side
            $trunk->b_id = $value['id'];
            $trunk->b_face = $value['face'];
            $trunk->b_partition = $value['partition'];
            $trunk->b_port = $value['port_id'];

            // Save new trunk object
            $trunk->save();

            array_push($returnData['add'], $trunk->toArray());
        }

        return $returnData;
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
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:trunk'
            ]
        ];
        $validatorMessages = [
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();
				
		$trunk = TrunkModel::where('id', $id)->first();
        $trunk->delete();

        return array('id' => $id);
    }
}
