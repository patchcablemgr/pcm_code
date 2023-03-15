<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use App\Models\CategoryModel;
use App\Models\TemplateModel;
use App\Models\TemplateModelNoImgData;
use App\Models\ObjectModel;
use App\Models\LocationModel;
use App\Models\LocationModelNoImgData;
use App\Models\CableModel;
use App\Models\CablePathModel;
use App\Models\ConnectionModel;
use App\Models\PortModel;
use App\Models\TrunkModel;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\LocationController;

use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use File;
use Schema;

class ArchiveController extends Controller
{

    protected $jsonAttributes = array(
        'category' => array(),
        'template' => array(
            'blueprint'
        ),
        'location' => array(),
        'object' => array(
            'parent_partition_address',
            'parent_enclosure_address',
            'floorplan_address'
        ),
        'cable' => array(),
        'cable_path' => array(),
        'connection' => array(
            'a_partition',
            'b_partition'
        ),
        'port' => array(
            'object_partition'
        ),
        'trunk' => array(
            'a_partition',
            'b_partition'
        )
    );

    protected $imgAttributes = array(
        'category' => array(),
        'template' => array(
            'img_front',
            'img_rear'
        ),
        'location' => array(
            'img'
        ),
        'object' => array(),
        'cable' => array(),
        'cable_path' => array(),
        'connection' => array(),
        'port' => array(),
        'trunk' => array()
    );

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Addison

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $PCM = new PCM;

        $tables = array(
            'category' => new CategoryModel,
            'template' => new TemplateModelNoImgData,
            'location' => new LocationModelNoImgData,
            'object' => new ObjectModel,
            'cable' => new CableModel,
            'cable_path' => new CablePathModel,
            'connection' => new ConnectionModel,
            'port' => new PortModel,
            'trunk' => new TrunkModel,
        );

        $headers = array(
            'Content-Type' => 'application/zip'
        );

        // Open ZIP
        $zip = new \ZipArchive();
        $zipFilename = 'archive.zip';
        $zipFilePath = Storage::disk('local')->path($zipFilename);
        if($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {

            $tableColumnCallback = fn($column) => $column->Field;
            $tableEntryCallback = fn($row) => is_array($row) ? json_encode($row) : $row;

            foreach($tables as $table) {

                // Open temp stream to write CSV
                $csvFile = fopen('php://temp', 'w');

                // Retrieve list of table columns
                $tableName = $table->getTable();
                $tableColumns = DB::select('SHOW COLUMNS FROM '. $tableName);
                $tableColumns = array_map($tableColumnCallback, $tableColumns);

                // Add CSV header
                fputcsv($csvFile, $tableColumns);

                // Add CSV rows
                foreach ($table->all() as $entry) {

                    // Convert object cabinet_ru according to cabinet orientation
                    if($tableName == 'object') {
                        if($entry->cabinet_ru) {
                            $locationID = $entry->location_id;
                            $location = $tables['location']::where('id', $locationID)->first();
                            $cabinetRU = $entry->cabinet_ru;
                            $cabinetSize = $location->size;
                            $cabinetOrientation = $location->ru_orientation;
                            $entry->cabinet_ru = $PCM->getRUIndex($cabinetRU, $cabinetSize, $cabinetOrientation);
                        }
                    }

                    // Add row to CSV
                    fputcsv($csvFile, array_values(array_map($tableEntryCallback, $entry->toArray($entry))));

                    // Add image to ZIP
                    foreach($this->imgAttributes[$tableName] as $imgAttribute) {
                        if(isset($entry[$imgAttribute])) {
                            $imgFilename = $entry[$imgAttribute];
                            $imgSrcPath = Storage::disk('local')->path('images/'.$imgFilename);
                            $imgDstPath = 'images/'.$imgFilename;
                            $zip->addFile($imgSrcPath, $imgDstPath);
                        }
                    }
                }

                // Store CSV to disk
                $filename = $tableName.'.csv';
                Storage::disk('local')->put($filename, $csvFile);
                fclose($csvFile);

                // Add CSV to ZIP
                $zip->addFile(Storage::disk('local')->path($filename), $filename);
            }

            // Close ZIP
            $zip->close();

            // Return ZIP
            return response()->download(Storage::disk('local')->path($zipFilename));
        } else {
            throw ValidationException::withMessages(['error' => 'Not able to create archive.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        // Prepare variables
        $validatorInput = [
            'file' => $request->file
        ];
        $validatorRules = [
            'file' => [
                'mimetypes:application/zip',
                'max:2048'
            ]
        ];
        $validatorMessages = [];
        $customValidator = Validator::make($validatorInput, $validatorRules, $validatorMessages);
        $customValidator->stopOnFirstFailure();
        $customValidator->validate();

        // Store floorplan image
        $path = $request->file('file')->store('imports');

        // Get filename
        $pathArray = explode('/', $path);
        $filename = end($pathArray);
        $filePath = Storage::disk('local')->path('imports/'.$filename);
        $destPath = Storage::disk('local')->path('imports');

        $manifest = array(
            'trunk.csv',
            'template.csv',
            'port.csv',
            'object.csv',
            'location.csv',
            'connection.csv',
            'category.csv',
            'cable_path.csv',
            'cable.csv'
        );

        $tableSchemas = array(
            'category' => array(
                'filename' => 'category.csv',
                'controller' => new CategoryController,
                'model' => new CategoryModel,
                'post_method' => 'store',
                'patch_method' => 'update',
                'dependentAttrs' => array()
            ),
            'template' => array(
                'filename' => 'template.csv',
                'controller' => new TemplateController,
                'model' => new TemplateModel,
                'post_method' => 'store',
                'patch_method' => 'update',
                'dependentAttrs' => array()
            ),
            'location' => array(
                'filename' => 'location.csv',
                'controller' => new LocationController,
                'model' => new LocationModel,
                'post_method' => 'store',
                'patch_method' => 'update',
                'image_controller' => new ImageController,
                'image_controller_method' => 'storeLocationImage',
                'dependentAttrs' => array(
                    'parent_id'
                )
            ),
            'object' => array(
                'filename' => 'object.csv',
                'controller' => new ObjectController,
                'model' => new ObjectModel,
                'post_method' => array(
                    'standard' => 'storeStandard',
                    'insert' => 'storeInsert',
                    'floorplan' => 'storeFloorplan'
                ),
                'patch_method' => 'update',
                'dependentAttrs' => array(
                    'parent_id'
                )
            ),
            'trunk' => array(
                'filename' => 'trunk.csv',
                'controller' => new TrunkController,
                'model' => new TrunkModel,
                'post_method' => 'store',
                'patch_method' => 'update',
                'dependentAttrs' => array()
            ),
            'port' => array(
                'filename' => 'port.csv',
                'controller' => new PortController,
                'model' => new PortModel,
                'post_method' => 'store',
                'patch_method' => 'store',
                'dependentAttrs' => array()
            ),
            'connection' => array(
                'filename' => 'connection.csv',
                'controller' => new ConnectionController,
                'model' => new ConnectionModel,
                'post_method' => 'store',
                'patch_method' => 'store',
                'dependentAttrs' => array()
            ),
            'cable_path' => array(
                'filename' => 'cable_path.csv',
                'controller' => new CablePathController,
                'model' => new CablePathModel,
                'post_method' => 'store',
                'patch_method' => 'update',
                'dependentAttrs' => array()
            ),
            'cable' => array(
                'filename' => 'cable.csv',
                'controller' => new CableController,
                'model' => new CableModel,
                'post_method' => 'store',
                'patch_method' => 'update',
                'dependentAttrs' => array()
            ),
        );

        $zip = new \ZipArchive();
        if($zip->open($filePath) === TRUE) {
            
            for($x=0; $x<$zip->numFiles; $x++) {

                // Validate image filenames and add to manifest
                $zippedFilename = $zip->getNameIndex($x);
                if(preg_match("/^images\/[a-zA-Z0-9]{40}\.(jpg|png|gif)$/", $zippedFilename)) {
                    array_push($manifest, $zippedFilename);
                }

                // Validate file size
                $fileStat = $zip->statIndex($x);
                if($fileStat['size'] > 2048000) {
                    throw ValidationException::withMessages(['error' => 'Not able to extract files larger than 2MB.']);
                }
            }
            if($zip->extractTo($destPath, $manifest)) {

                foreach($tableSchemas as $tableName => $tableSchema) {
                    $tableFileName = $tableSchema['filename'];
                    Log::info($tableFileName);
                    $tableFilePath = Storage::disk('local')->path('imports/'.$tableFileName);
                    $file = fopen($tableFilePath, 'r');
                    $row = 1;
                    if($file !== FALSE) {
                        $entryIDArray = array();
                        //$postponedEntries = array();
                        while (($data = fgetcsv($file, 100000, ",")) !== FALSE) {

                            if($row == 1){

                                // Process header
                                $attrMap = $this->processCSVHeader($tableName, $data);

                            } else {

                                /*
                                // Postpone processing entries that depend on other entries which have not been processed yet
                                foreach($tableSchema['dependentAttrs'] as $dependentAttr) {
                                    $depAttrIdx = $attrMap[$dependentAttr];
                                    $depID = $data[$depAttrIdx];
                                    if(!in_array($depID, $entryIDArray)) {
                                        $postponedEntries[$row] = $data;
                                        $row++;
                                        continue;
                                    }
                                }
                                */

                                // Prevent entries with duplicate IDs
                                $entryIDIdx = $attrMap['id'];
                                $entryID = $data[$entryIDIdx];
                                if(in_array($entryID, $entryIDArray)) {
                                    throw ValidationException::withMessages(['error' => 'Duplicate entry ID found. '.$tableFileName.":".$row]);
                                }

                                // Process entry
                                $importIDArray = $this->processCSVEntry($data, $attrMap, $tableSchema, $tableName, $tableFileName, $row);

                                // Add IDs to entry array
                                foreach($importIDArray as $importID) {
                                    array_push($entryIDArray, $importID);
                                }
                            }
                            $row++;
                        }
                        
                    } else {
                        throw ValidationException::withMessages(['error' => 'Not able to open extracted file.']);
                    }
                    fclose($file);

                    // Delete entries
                    foreach($tableSchema['model']->all() as $dbEntry) {
                        $dbEntryID = $dbEntry['id'];
                        if(!in_array($dbEntryID, $entryIDArray)) {

                            // Set a reference to the archive for helpful error reporting
                            $tableSchema['controller']->archiveAddress = $tableFileName.":".$row;
                            
                            // Submit DELETE request
                            $newResponse = call_user_func(array($tableSchema['controller'], 'destroy'), $dbEntryID);
                        }
                    }
                }
            } else {
                throw ValidationException::withMessages(['error' => 'Not able to extract archive.']);
            }
        } else {
            throw ValidationException::withMessages(['error' => 'Not able to open archive.']);
        }

        return true;
    }

    /**
     * process CSV Header by mapping header to index value
     *
     * @param   string $tableName
     * @param   array $data
     * @return  array
     */
    private function processCSVHeader($tableName, $data)
    {

        $attrMap = array();

        foreach($data as $idx=>$attr) {
            $attrMap[$attr] = $idx;
        }

        return $attrMap;
    }

    /**
     * process CSV Header by mapping header to index value
     *
     * @param   string $tableName
     * @param   array $data
     * @param   array $attrMap
     * @param   array $tableSchema
     * @param   string $tableName
     * @param   string $tableFileName
     * @param   integer $row
     * @return  integer
     */
    private function processCSVEntry($data, $attrMap, $tableSchema, $tableName, $tableFileName, $row)
    {

        // Create request
        $importRequest = new Request;

        // Get entry ID
        $entryID = $data[$attrMap['id']];

        // Determine if entry should be created or updated
        $entryAction = ($entryID) ? 'update' : 'create';

        // Prepare request
        switch($entryAction) {

            case 'create':
                Log::info('Create: '.$tableFileName.":".$row);
                $importRequest->setMethod('POST');
                $controllerMethod = $tableSchema['post_method'];

                // Determine controller method
                if($tableName == 'object') {

                    // Object controller
                    if($data[$attrMap['parent_id']] != NULL) {
                        $controllerMethod = $tableSchema['post_method']['insert'];
                    } else if($data[$attrMap['floorplan_address']] != NULL) {
                        $controllerMethod = $tableSchema['post_method']['floorplan'];
                    } else {
                        $controllerMethod = $tableSchema['post_method']['standard'];
                    }
                }
                break;

            case 'update':
                Log::info('Update: '.$tableFileName.":".$row);
                $importRequest->setMethod('PATCH');
                $controllerMethod = $tableSchema['patch_method'];
                break;
        }

        // Compile request data
        $requestData = array();
        foreach(array_keys($attrMap) as $attr) {

            // Get array index of attribute
            $attrIdx = $attrMap[$attr];

            // Decode JSON if necessary
            $requestDataEntry = (in_array($attr, $this->jsonAttributes[$tableName])) ? json_decode($data[$attrIdx], true) : $requestDataEntry = $data[$attrIdx];
            
            // Add attribute to request data
            $requestData[$attr] = $requestDataEntry;
        }

        // Set a reference to the archive for helpful error reporting
        $tableSchema['controller']->archiveAddress = $tableFileName.":".$row;

        // Submit request
        $importRequest->request->add($requestData);
        $importResponse = call_user_func(array($tableSchema['controller'], $controllerMethod), $importRequest, $entryID);
        $importIDArray = array();

        // Store added row IDs
        if(isset($importResponse['add'])) {
            foreach($importResponse['add'] as $import) {
                array_push($importIDArray, $import['id']);
            }
        } else {
            array_push($importIDArray, $importResponse['id']);
        }

        // Process images
        foreach($this->imgAttributes[$tableName] as $imgAttr) {
            $attrIdx = $attrMap[$imgAttr];
            $imgFileName = $data[$attrIdx];
            if($imgFileName) {
                $imgFilePath = Storage::disk('local')->path('imports/images/'.$imgFileName);
                $imgMimeType = Storage::mimeType('imports/images/'.$imgFileName);
                $imgFile = array('file' => new UploadedFile($imgFilePath, $imgFileName, $imgMimeType, null, true));
                $imgRequest = (new Request())->duplicate([], [], [], [], $imgFile);
                $imageResponse = call_user_func(array($tableSchema['image_controller'], $tableSchema['image_controller_method']), $imgRequest, $importIDArray[0]);
            }
        }

        return $importIDArray;
    }
}
