<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaTypeModel;

class AttributesMediaType extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mediaType = MediaTypeModel::all();
        return $mediaType->toArray();
    }

}
