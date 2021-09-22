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
                Rule::requiredIf($request->type == 'standard'),
                'integer',
                'min:1',
                'max:25'
            ],
            'function' => [
                'required',
                Rule::in($templateFunctionArray)
            ],
            'mount_config' => [
                Rule::requiredIf($request->type == 'standard'),
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
        $template->function = $request->function;
        $template->blueprint = $request->blueprint;
				
				if($request->type == 'insert') {
					
					// Prepare some variables
					$parentTemplateData = $request->parent_template;
					$parentTemplateID = $parentTemplateData['id'];
					$parentTemplateFace = $parentTemplateData['face'];
					$parentTemplatePartitionAddress = $parentTemplateData['partition_address'];
					
					// Prepare parent partition address
					$parentTemplatePartitionParentAddress = $parentTemplatePartitionAddress;
					array_pop($parentTemplatePartitionParentAddress);

					// Get parent template partition units
					$parentTemplate = TemplateModel::where('id', '=', $parentTemplateID)->first();
					$parentTemplateBlueprint = $parentTemplate['blueprint'];
					$parentTemplatePartition = $PCM->getPartition($parentTemplateBlueprint, $parentTemplateFace, $parentTemplatePartitionAddress);
					$parentTemplatePartitionUnits = $parentTemplatePartition['units'];
					
					// Get parent template partition RU size
					$parentTemplatePartitionParentRUSize = $parentTemplate['ru_size'];
					
					// Get parent template partition ... parent partition units
					if(count($parentTemplatePartitionParentAddress)) {
						$parentTemplatePartitionParentPartition = $PCM->getPartition($parentTemplateBlueprint, $parentTemplateFace, $parentTemplatePartitionParentAddress);
						$parentTemplatePartitionParentUnits = $parentTemplatePartitionParentPartition['units'];
					} else {
						$parentTemplatePartitionParentUnits = $parentTemplatePartitionParentRUSize * 2;
					}
					
					// Determine parent template partition direction
					$parentTemplatePartitionDirection = (count($parentTemplatePartitionAddress) % 2) ? 'row' : 'col';
					
					//  Compile insert constraints data
					if($parentTemplatePartitionDirection == 'row') {
						$parentPortLayout = array('height' => $parentTemplatePartitionParentUnits, 'width' => $parentTemplatePartitionUnits);
					} else {
						$parentPortLayout = array('height' => $parentTemplatePartitionUnits, 'width' => $parentTemplatePartitionParentUnits);
					}
					$parentEncLayout = $parentTemplatePartition['enc_layout'];
					$insertConstraints = array(
						'part_layout' => $parentPortLayout,
						'enc_layout' => $parentEncLayout
					);
					
					$template->insert_constraints = [$insertConstraints];
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
