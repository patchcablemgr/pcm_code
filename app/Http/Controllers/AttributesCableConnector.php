<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CableConnectorModel;

class AttributesCableConnector extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $CableConnector = CableConnectorModel::all();
        return $CableConnector->toArray();
    }
}
