<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;
use App\Models\ConnectionModel;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $connections = ConnectionModel::all();

        return $connections;
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

        // Find connections associated with selected object to be removed
        $filteredConnections = ConnectionModel::where(
            [
                ['a_id', $data['id']],
                ['a_face', $data['face']],
                ['a_partition', json_encode($data['partition'])],
                ['a_port', $data['port_id']]
            ]
        )->orwhere(
            [
                ['b_id', $data['id']],
                ['b_face', $data['face']],
                ['b_partition', json_encode($data['partition'])],
                ['b_port', $data['port_id']]
            ]
        )->get();
        $connectionDeleteArray = $filteredConnections->all();

        // Find connections associated with selected node(s) to be removed
        foreach($data['PeerData'] as $key => $value) {

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

            $filteredConnections = ConnectionModel::where(
                $aAttributes
            )->orwhere(
                $bAttributes
            )->get();
            $connectionDeleteArray = array_merge($connectionDeleteArray, $filteredConnections->all());

        }

        // Delete any existing connection records
        foreach($connectionDeleteArray as $connectionDelete) {
            array_push($returnData['remove'], $connectionDelete);
            ConnectionModel::where('id', $connectionDelete['id'])->delete();
        }

        // Create new connection record(s)
        foreach($data['PeerData'] as $key => $value) {

            // Create new connection object
            $connection = new ConnectionModel;

            // Store A side
            $connection->a_id = $data['id'];
            $connection->a_face = $data['face'];
            $connection->a_partition = $data['partition'];
            $connection->a_port = $data['port_id'];

            // Store B side
            $connection->b_id = $value['id'];
            $connection->b_face = $value['face'];
            $connection->b_partition = $value['partition'];
            $connection->b_port = $value['port_id'];

            // Save new connection object
            $connection->save();

            array_push($returnData['add'], $connection->toArray());
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
        //
    }
}
