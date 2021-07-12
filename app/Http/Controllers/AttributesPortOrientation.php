<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortOrientation;

class AttributesPortOrientation extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $portOrientation = PortOrientation::all();
        return $portOrientation->toArray();
    }

}
