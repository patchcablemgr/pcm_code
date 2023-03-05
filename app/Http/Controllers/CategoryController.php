<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\CategoryModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{

    public $archiveAddress = NULL;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = CategoryModel::all();
        return $categories->toArray();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCatalogCategories()
    {
        
        $PCM = new PCM;
        $catalogCategories = $PCM->fetchCatalogCategories();

        return $catalogCategories;
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
            'color' => [
                'required',
                'regex:/^\#[\dA-F]{8}$/'
            ],
            'default' => [
                'required',
                'boolean'
            ],
        ];

        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $category = new CategoryModel;

        $category->name = $request->name;
        $category->color = $request->color;
        $category->default = 0;

        // New category will be default
        if($request->default) {

            // Remove default status from existing default category
            $defaultCategories = CategoryModel::all()->where('default', true);
            foreach($defaultCategories as $defaultCategory) {
                $defaultCategory->default = 0;
                $defaultCategory->save();
            }

            // Set default status of new category to true
            $category->default = 1;
        }

        $category->save();

        return $category->toArray();
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

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $PCM = new PCM;

        $defaultCategory = CategoryModel::where('default', '=', 1)->first();

        $request->request->add(['id' => $id]);

        $validatorRules = [
            'id' => [
                'required',
                'numeric',
                'exists:category',
            ],
            'name' => [
                'alpha_dash',
                'min:1',
                'max:255',
            ],
            'color' => [
                'regex:/^\#[\dA-F]{8}$/'
            ],
            'default' => [
                'boolean'
            ]
        ];
        $validatorMessages = $PCM->transformValidationMessages($validatorRules, $this->archiveAddress);
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Store category
        $category = CategoryModel::where('id', $id)->first();

        // Update details
        foreach($request->all() as $field => $value) {
            switch($field) {
                case 'name':
                    $category->name = $value;
                    break;

                case 'color':
                    $category->color = $value;
                    break;

                case 'default':
                    
                    if($value) {
                        CategoryModel::where('default', '=', 1)->update(['default' => false]);
                    }
                    
                    $category->default = $value;
                    break;
            }
        }

        $category->save();

        return $category->toArray();
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
                'exists:category',
                'unique:App\Models\TemplateModel,category_id'
            ]
        ];
        $validatorMessages = [
            'id.unique' => 'The category is in use and cannot be deleted.'
        ];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        $category = CategoryModel::where('id', $id)->first();

        $category->delete();

        return array('id' => $id);
    }

}
