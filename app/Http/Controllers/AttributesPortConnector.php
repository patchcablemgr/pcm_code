<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortConnectorModel;

class AttributesPortConnector extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $PortConnector = PortConnectorModel::all();
        return $PortConnector->toArray();
    }
}
