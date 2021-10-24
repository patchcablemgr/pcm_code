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

        $PCM = new PCM;

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

                // Update template name
                $template->name = $value;
            } else if($key == 'category_id') {

                // Update template category ID
                $template->category_id = $value;
            } else if($key == 'port_format') {
                
                // Update connectable port format
                $templateID = $value['template_id'];
                $templateFace = $value['template_face'];
                $templatePartitionAddress = $value['template_partition'];
                $templateBlueprint = $template['blueprint'];
                $templatePartition = $PCM->getPartition($templateBlueprint, $templateFace, $templatePartitionAddress);
                $templatePortFormat = $templatePartition['port_format'];
                $portFormatIndex = $value['port_format_index'];
                $portFormatAttr = $value['port_format_attr'];
                $portFormatValue = $value['port_format_value'];
                
                if($portFormatAttr == 'value') {

                    // Update port format field data
                    $templatePartition['port_format'][$portFormatIndex]['value'] = $portFormatValue;

                } else if($portFormatAttr == 'type') {

                    $currentType = $templatePortFormat[$portFormatIndex]['type'];
                    $currentOrder = $templatePortFormat[$portFormatIndex]['order'];
                    $currentIsIncremental = ($currentType == 'series' || $currentType == 'incremental') ? true : false;
                    $newIsIncremental = ($portFormatValue == 'series' || $portFormatValue == 'incremental') ? true : false;
                    $newOrder = ($newIsIncremental) ? 1 : 0;

                    // Addjust incremental field order
                    forEach($templatePartition['port_format'] as $fieldIndex => &$fieldValue) {

                        $fieldType = $fieldValue['type'];
                        $fieldOrder = $fieldValue['order'];
                        $fieldIsIncremental = ($fieldType == 'series' || $fieldType == 'incremental') ? true : false;

                        // static -> incremental
                        if(!$currentIsIncremental && $newIsIncremental) {
                            if($fieldIsIncremental) {
                                $newOrder++;
                            }

                        // incremental -> static
                        } else if($currentIsIncremental && !$newIsIncremental) {
                            if($fieldIsIncremental && $fieldOrder >= $currentOrder) {
                                $fieldValue['order']--;
                            }
                        }
                    }

                    // Update port format field data
                    $templatePartition['port_format'][$portFormatIndex]['type'] = $portFormatValue;
                    $templatePartition['port_format'][$portFormatIndex]['order'] = $newOrder;
                } else if($portFormatAttr == 'count') {

                    // Update port format field data
                    $templatePartition['port_format'][$portFormatIndex]['count'] = $portFormatValue;
                } else if($portFormatAttr == 'order') {

                    $portFormatValueOrig = $templatePartition['port_format'][$portFormatIndex]['order'];

                    // Update port format field data
                    $templatePartition['port_format'][$portFormatIndex]['order'] = $portFormatValue;

                    // Addjust incremental field order
                    forEach($templatePartition['port_format'] as $fieldIndex => &$fieldValue) {

                        $fieldType = $fieldValue['type'];
                        $fieldOrder = $fieldValue['order'];
                        $fieldIsIncremental = ($fieldType == 'series' || $fieldType == 'incremental') ? true : false;

                        if($fieldIsIncremental && $fieldIndex != $portFormatIndex) {

                            //if(PortFormatFieldOrder > PortFormatValue && PortFormatFieldOrder < PortFormatValueOrig) {
                            if($fieldOrder > $portFormatValue && $fieldOrder < $portFormatValueOrig) {

                                $fieldValue['order']++;
                            }else if($fieldOrder < $portFormatValue && $fieldOrder > $portFormatValueOrig) {

                                $fieldValue['order']--;
                            }else if($fieldOrder == $portFormatValue) {

                                if($portFormatValue > $portFormatValueOrig) {

                                    $fieldValue['order']--;
                                } else if($portFormatValue < $portFormatValueOrig) {

                                    $fieldValue['order']++;
                                }
                            }
                        }
                    }
                } else if($portFormatAttr == 'position') {

                    $portFormatField = array_slice($templatePartition['port_format'], $portFormatIndex, 1);

                    array_splice($templatePartition['port_format'], $portFormatIndex, 1);
                    array_splice($templatePartition['port_format'], $portFormatValue, 0, $portFormatField);

                } else if($portFormatAttr == 'create') {

                    // Insert default port field format at insert location
                    array_splice($templatePartition['port_format'], $portFormatValue, 0, [$PCM->defaultPortFormatField]);

                } else if($portFormatAttr == 'delete') {

                    $portFormatDeletedOrder = $templatePartition['port_format'][$portFormatIndex]['order'];

                    // Remove port format field
                    array_splice($templatePartition['port_format'], $portFormatIndex, 1);
                    
                    // Addjust incremental field order
                    forEach($templatePartition['port_format'] as $fieldIndex => &$fieldValue) {

                        $fieldType = $fieldValue['type'];
                        $fieldOrder = $fieldValue['order'];
                        $fieldIsIncremental = ($fieldType == 'series' || $fieldType == 'incremental') ? true : false;

                        if($fieldIsIncremental) {

                            if($fieldOrder > $portFormatDeletedOrder) {
                                $fieldValue['order']--;
                            }
                        }
                    }
                }

                // Patch partition with updated data
                $templateBlueprintNew = $PCM->patchPartition($templateBlueprint, $templateFace, $templatePartitionAddress, $templatePartition);

                // Update template blueprint
                if($templateBlueprintNew) {
                    $template->blueprint = $templateBlueprintNew;
                }
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
