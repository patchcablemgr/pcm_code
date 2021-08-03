<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\TemplateModel;
use App\Rules\TemplateBlueprint;
use Illuminate\Support\Facades\Log;

class Template extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = TemplateModel::all();
        return $templates->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Prepare variables
        $templateTypeArray = [
            'standard',
            'insert'
        ];
        $templateFunctionArray = [
            'endpoint',
            'passive'
        ];
        $templateMountConfigArray = [
            '2-post',
            '4-post'
        ];

        $request->validate([
            'name' => [
                'required',
                'alpha_dash',
                'unique:App\Models\TemplateModel,name',
                'min:1',
                'max:255'
            ],
            'category_id' => [
                'required',
                'exists:template_category,id'
            ],
            'type' => [
                'required',
                Rule::in($templateTypeArray)
            ],
            'ru_size' => [
                'required',
                'integer',
                'min:1',
                'max:25'
            ],
            'function' => [
                'required',
                Rule::in($templateFunctionArray)
            ],
            'mount_config' => [
                'required',
                Rule::in($templateMountConfigArray)
            ],
            'blueprint' => [
                'required',
                new TemplateBlueprint
            ]
        ]);

        $template = new TemplateModel;

        $template->name = $request->name;
        $template->category_id = $request->category_id;
        $template->type = $request->type;
        $template->ru_size = $request->ru_size;
        $template->function = $request->function;
        $template->mount_config = $request->mount_config;
        $template->blueprint = json_encode($request->blueprint);

        $template->save();

        return $template->toArray();
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
