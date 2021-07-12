<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;

class AttributesMedia extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media = Media::all();
        return $media->toArray();
    }

}
