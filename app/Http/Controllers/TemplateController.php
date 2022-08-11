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
use Illuminate\Support\Facades\Gate;

class TemplateController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCatalogTemplates()
    {
        
        $PCM = new PCM;
        $catalogTemplates = $PCM->fetchCatalogTemplates();

        return $catalogTemplates;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importCatalogTemplate($id)
    {
        
        $PCM = new PCM;

        // Validate template ID
        $validatorInput = [
            'id' => $id,
        ];
        $validatorRules = [
            'id' => [
                'required',
                'numeric',
            ],
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $template = $PCM->importCatalogTemplate($id);

        return $catalogTemplates;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

		$PCM = new PCM;

        $request->validate([
            'name' => [
                'required',
                'alpha_dash',
                'min:1',
                'max:255'
            ],
            'category_id' => [
                'required',
                'exists:category,id'
            ],
            'type' => [
                'required',
                'in:standard,insert',
            ],
            'ru_size' => [
                'exclude_unless:type,standard',
                'integer',
                'min:1',
                'max:25'
            ],
            'function' => [
                'required',
                'in:endpoint,passive',
            ],
            'mount_config' => [
                'exclude_unless:type,standard',
                'in:2-post,4-post',
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
            ],
            'blueprint.front' => [
                'required',
                'min:1',
                'max:100',
                new TemplateBlueprint($request->ru_size)
            ],
            'blueprint.rear' => [
                'required',
                'min:1',
                'max:100',
                new TemplateBlueprint($request->ru_size)
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $PCM = new PCM;

        // Validate template ID
        $validatorInput = [
            'id' => $id,
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id')
        ];
        $validatorRules = [
            'id' => [
                'required',
                'numeric',
                'exists:template'
            ],
            'name' => [
                'required',
                'alpha_dash',
                'min:1',
                'max:255',
            ],
            'category_id' => [
                'required',
                'numeric',
                'exists:category,id'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

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
            } else if($key == 'blueprint') {

                $templateBlueprint = $template->blueprint;
                foreach($value as $face => $inputBlueprint) {
                    $PCM->patchBlueprint($templateBlueprint[$face], $inputBlueprint);
                }
                $template->blueprint = $templateBlueprint;
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

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }
        
		$validatorInput = [
            'id' => $id
        ];
        $validatorRules = [
            'id' => [
                'required',
                'exists:template',
                'unique:App\Models\ObjectModel,template_id'
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
