<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\TemplateModel;
use App\Http\Controllers\PCM;
use App\Rules\TemplateBlueprint;
use App\Rules\TemplateInsertParentData;
use Illuminate\Support\Facades\Log;

class Templates extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = TemplateModel::all();

        return $templates;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

		$PCM = new PCM;
		
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
                'exclude_unless:type,standard',
                'integer',
                'min:1',
                'max:25'
            ],
            'function' => [
                'required',
                Rule::in($templateFunctionArray)
            ],
            'mount_config' => [
                'exclude_unless:type,standard',
                Rule::in($templateMountConfigArray)
            ],
            'insert_constraints.part_layout.height' => [
                'exclude_unless:type,insert',
                'integer'
            ],
            'insert_constraints.part_layout.width' => [
                'exclude_unless:type,insert',
                'integer'
            ],
            'insert_constraints.enc_layout.cols' => [
                'exclude_unless:type,insert',
                'integer'
            ],
            'insert_constraints.enc_layout.rows' => [
                'exclude_unless:type,insert',
                'integer'
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
        $template->ru_size = null;
        $template->function = $request->function;
        $template->mount_config = null;
        $template->insert_constraints = null;
        $template->blueprint = $request->blueprint;
				
        if($request->type == 'insert') {
            
            $template->insert_constraints = $request->insert_constraints;
        } else {

            $template->ru_size = $request->ru_size;
            $template->mount_config = $request->mount_config;
        }

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

        // Validate template ID
        $validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:template'
            ]
        ];
        $validatorMessages = [
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Validate request data
        $request->validate([
            'name' => [
                'alpha_dash',
                'unique:App\Models\TemplateModel,name',
                'min:1',
                'max:255'
            ],
            'category_id' => [
                'exists:template_category,id'
            ],
        ]);

        // Store request data
        $data = $request->all();

        // Retrieve template record
        $template = TemplateModel::where('id', $id)->first();

        // Update template record
        foreach($data as $key => $value) {
            if($key == 'name') {
                $template->name = $value;
            } else if($key == 'category_id') {
                $template->category_id = $value;
            }
        }

        // Save template record
        $template->save();

        // Return template record
        return $template->toArray();
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
                'exists:template',
                'unique:App\Models\ObjectsModel,template_id'
            ]
        ];
        $validatorMessages = [
            'id.unique' => 'The template is in use and cannot be deleted.'
        ];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();
				
		$template = TemplateModel::where('id', $id)->first();

        $template->delete();

        return array('id' => $id);
    }
}
