<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CategoryModel;
use App\Models\LocationModel;
use App\Models\ObjectModel;
use App\Models\TemplateModel;
use App\Models\ConnectionModel;
use App\Models\CablePathModel;
use App\Models\TrunkModel;

class DataSyncController extends Controller
{
    /**
     * Get creates/updates since timestamp
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $timestamp
     * @return \Illuminate\Http\Response
     */
    public function getChanges(Request $request, $timestamp)
    {
        // Validate template ID
        $validatorInput = [
            'timestamp' => $timestamp,
        ];
        $validatorRules = [
            'timestamp' => [
                'required',
                'numeric',
            ],
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Format timestamp
        $dateFormatted = date('Y-m-d H:i:s', $timestamp);

        // Collect tables
        $categories = CategoryModel::where('updated_at', '>', $dateFormatted)->get();
        $locations = LocationModel::where('updated_at', '>', $dateFormatted)->get();
        $objects = ObjectModel::where('updated_at', '>', $dateFormatted)->get();
        $templates = TemplateModel::where('updated_at', '>', $dateFormatted)->get();
        $connections = ConnectionModel::where('updated_at', '>', $dateFormatted)->get();
        $cablePaths = CablePathModel::where('updated_at', '>', $dateFormatted)->get();
        $trunks = TrunkModel::where('updated_at', '>', $dateFormatted)->get();

        // Compile tables
        $returnData = array(
            'tables' => array(
                'categories' => $categories,
                'locations' => $locations,
                'objects' => $objects,
                'templates' => $templates,
                'connections' => $connections,
                'cable-paths' => $cablePaths,
                'trunks' => $trunks,
            ),
            'timestamp' => time()
        );

        // Return tables
        return $returnData;
    }
}
