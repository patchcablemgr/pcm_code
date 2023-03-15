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
use App\Models\CableModel;
use App\Models\PortModel;
use Illuminate\Support\Facades\Log;

class DataSyncController extends Controller
{
    /**
     * Get creates/updates since timestamp
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $timestamp
     * @return \Illuminate\Http\Response
     */
    public function getChanges(Request $request)
    {
        
        $validatorRules = [
            'timestamp' => [
                'required',
                'numeric',
            ],
            'entries' => [
                'required',
                'array:cable-paths,cables,categories,connections,locations,objects,ports,templates,trunks',
            ],
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($request->all(), $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Format timestamp
        $dateFormatted = date('Y-m-d H:i:s', $request->timestamp);

        $tableModels = array(
            'cable-paths' => new CablePathModel,
            'cables' => new CableModel,
            'categories' => new CategoryModel,
            'connections' => new ConnectionModel,
            'locations' => new LocationModel,
            'objects' => new ObjectModel,
            'ports' => new PortModel,
            'templates' => new TemplateModel,
            'trunks' => new TrunkModel,
        );

        foreach($tableModels as $tableName => $tableModel) {

            $presentArray = $tableModel->where('updated_at', '>', $dateFormatted)->get();
            $absentArray = $this->getDeletes($tableModel, $request->entries[$tableName]);
            $tables[$tableName] = array(
                'present' => $presentArray,
                'absent' => $absentArray
            );
        }

        // Compile tables
        $returnData = array(
            'tables' => $tables,
            'timestamp' => time()
        );

        // Return tables
        return $returnData;
    }

    /**
     * Get creates/updates since timestamp
     *
     * @param  object   $tableModel
     * @param  array    $entries
     * @return array
     */
    private function getDeletes($tableModel, $entries)
    {

        // Initialize return array
        $deleteArray = array();

        // Iterate over entry IDs
        foreach($entries as $entryID) {

            // Push entry ID if it is not found in the database
            if($tableModel->where('id', '=', $entryID)->count() == 0) {
                array_push($deleteArray, $entryID);
            }
        }

        return $deleteArray;
    }
}
