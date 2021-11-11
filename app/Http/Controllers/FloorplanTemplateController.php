<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FloorplanTemplateModel;

class FloorplanTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $FloorplanTemplates = FloorplanTemplateModel::all();
        return $FloorplanTemplates->toArray();
    }

}
