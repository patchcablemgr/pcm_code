<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\CategoryModel;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'alpha_dash', 'unique:App\Models\CategoryModel,name', 'min:1', 'max:255'],
            'color' => ['required', 'regex:/^\#[\dA-F]{8}$/'],
            'default' => ['required', 'boolean'],
        ]);

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

        $defaultCategory = CategoryModel::where('default', '=', 1)->first();

        // Validate template ID
        $validatorInput = [
            'id' => $id,
            'name' => $request->input('name'),
            'color' => $request->input('color'),
            'default' => $request->input('default')
        ];
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
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
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
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        $category = CategoryModel::where('id', $id)->first();

        $category->delete();

        return array('id' => $id);
    }

}
