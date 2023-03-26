<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\TemplateModel;
use App\Models\CategoryModel;
use App\Http\Controllers\PCM;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Rules\TemplateBlueprint;
use App\Rules\TemplateInsertParentData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class TemplateController extends Controller
{

    public $archiveAddress = NULL;

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

        // Retrieve template and category data
        $responseData = $PCM->fetchCatalogTemplate($id)->json();
        $template = $responseData['template'];
        $category = $responseData['category'];

        // Attempt to find category
        $existingCategory = CategoryModel::where('name', $category['name'])->first();

        // Create category if it doesn't exist and store category ID
        if(is_null($existingCategory)) {

            // Create new category
            $categoryController = new CategoryController;
            $categoryRequest = new Request;
            $categoryRequest->setMethod('POST');
            $categoryRequest->request->add($category);
            $newCategory = $categoryController->store($categoryRequest);

            // Store category ID
            $categoryID = $newCategory['id'];
        } else {

            // Store category
            $newCategory = false;

            // Store category ID
            $categoryID = $existingCategory['id'];
        }

        // Update template category ID
        $template['category_id'] = $categoryID;

        // Store template
        $templateRequest = new Request;
        $templateRequest->setMethod('POST');
        $templateRequest->request->add($template);
        $newTemplate = $this->store($templateRequest);

        // Store front image if it exists
        $faceArray = array('front', 'rear');

        foreach($faceArray as $face) {

            $imgData = $template['img_'.$face];

            if($imgData) {

                // $imgData = data:image/jpeg;base64,/9j/4AAQSkZJRgABAQE
                list($preamble, $imgBase64) = explode(',', $imgData);
                $imgBase64 = str_replace(' ', '+', $imgBase64);
                // $preamble = data:image/jpeg;base64
                // $imgBase64 = /9j/4AAQSkZJRgABAQE
                list($metaData,) = explode(';', $preamble);
                // $metaData = data:image/jpeg
                list(, $mimeType) = explode(':', $metaData);
                // $mimeType = image/jpeg
                list(, $fileExt) = explode('/', $mimeType);

                $fileName = 'import-temp.'.$fileExt;
                $filePath = 'images/'.$fileName;

                Storage::disk('local')->put($filePath, base64_decode($imgBase64));
                $file = array('file' => new UploadedFile(base_path('storage/app/'.$filePath), $fileName, $mimeType, null, true));
                $data = array('face' => $face);
                $request = (new Request())->duplicate($data, [], [], [], $file);

                $templateImageController = new ImageController;
                $newTemplate = $templateImageController->storeTemplateImage($request, $newTemplate['id']);

            }
        }

        $returnData = array(
            'template' => $newTemplate,
            'category' => $newCategory
        );

        return $returnData;
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

        $validatorRules = [
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
                'exclude_if:type,insert',
                'integer',
                'min:1',
                'max:25'
            ],
            'function' => [
                'required',
                'in:endpoint,passive',
            ],
            'mount_config' => [
                'exclude_if:type,insert',
                'in:2-post,4-post',
            ],
            'insert_constraints.part_layout.height' => [
                'exclude_if:type,standard',
                'integer'
            ],
            'insert_constraints.part_layout.width' => [
                'exclude_if:type,standard',
                'integer'
            ],
            'insert_constraints.enc_layout.cols' => [
                'exclude_if:type,standard',
                'integer'
            ],
            'insert_constraints.enc_layout.rows' => [
                'exclude_if:type,standard',
                'integer'
            ],
            'blueprint' => [
                'required',
            ],
            'blueprint.front' => [
                'required',
                'min:1',
                'max:100',
                new TemplateBlueprint($request, null, 'front', $this->archiveAddress)
            ],
            'blueprint.rear' => [
                'required',
                'min:1',
                'max:100',
                new TemplateBlueprint($request, null, 'rear', $this->archiveAddress)
            ]
        ];

        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();
				
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

        $request->request->add(['id' => $id]);
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
            ],
            'blueprint' => [
                'required',
            ],
            'blueprint.front' => [
                'required',
                'min:1',
                'max:100',
                new TemplateBlueprint(null, $id, 'front', $this->archiveAddress)
            ],
            'blueprint.rear' => [
                'required',
                'min:1',
                'max:100',
                new TemplateBlueprint(null, $id, 'rear', $this->archiveAddress)
            ]
        ];
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
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
