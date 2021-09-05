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
						'parent_template.id' => [
								Rule::requiredIf($request->type == 'insert')
						],
						'parent_template.face' => [
								Rule::requiredIf($request->type == 'insert')
						],
						'parent_template.partition_address' => [
								Rule::requiredIf($request->type == 'insert')
						],
						'parent_template' => [
								new TemplateInsertParentData
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
				
				if($request->type == 'insert') {
					
					$parentTemplateData = $request->parent_template;
					$parentTemplateID = $parentTemplateData['id'];
					$parentTemplateFace = $parentTemplateData['face'];
					$parentTemplatePartitionAddress = $parentTemplateData['partition_address'];
					
					$parentTemplate = TemplateModel::where('id', '=', $parentTemplateID)->first();
					$parentTemplateBlueprint = $parentTemplate['blueprint'];
					$parentTemplatePartition = $PCM->getPartition($parentTemplateBlueprint, $parentTemplateFace, $parentTemplatePartitionAddress);
					
					$parentPartLayout = array(
						'cols' => $parentTemplatePartition['units'],
						'rows' => $parentTemplatePartition['units'],
					);
					$parentEncLayout = $parentTemplatePartition['enc_layout'];
					$insertConstraints = array(
						'part_layout' => $parentPartLayout,
						'enc_layout' => $parentEncLayout
					);
					
					$template->insert_constraints = json_encode([$insertConstraints]);
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
