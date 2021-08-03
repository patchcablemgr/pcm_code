<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MediaModel;

class AttributesMedia extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media = MediaModel::all();
        return $media->toArray();
    }

}
