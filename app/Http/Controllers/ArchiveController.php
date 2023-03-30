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
            'blueprint',
            'insert_constraints'
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

    protected $staticAttributes = array(
        'category' => array(),
        'template' => array(
            'type',
            'ru_size',
            'function',
            'mount_config',
            'insert_constraints',
        ),
        'location' => array(
            'type',
        ),
        'object' => array(
            'template_id',
            'floorplan_object_type'
        ),
        'cable' => array(),
        'cable_path' => array(),
        'connection' => array(),
        'port' => array(),
        'trunk' => array()
    );

    protected $tableSchemas = array(
        'category' => array(
            'filename' => 'category.csv',
            'controller' => 'App\Http\Controllers\CategoryController',
            'model' => 'App\Models\CategoryModel',
            'post_method' => 'store',
            'patch_method' => 'update',
            'image_controller' => false,
            'image_controller_method' => false,
            'dependent_attr' => array(
                'id' => array(
                    'template' => array(
                        'category_id'
                    )
                )
            ),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'template' => array(
            'filename' => 'template.csv',
            'controller' => 'App\Http\Controllers\TemplateController',
            'model' => 'App\Models\TemplateModelNoImgData',
            'post_method' => 'store',
            'patch_method' => 'update',
            'image_controller' => 'App\Http\Controllers\ImageController',
            'image_controller_method' => 'storeTemplateImage',
            'dependent_attr' => array('id' => array(
                'object' => array(
                    'template_id'
                )
            )),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'location' => array(
            'filename' => 'location.csv',
            'controller' => 'App\Http\Controllers\LocationController',
            'model' => 'App\Models\LocationModelNoImgData',
            'post_method' => 'store',
            'patch_method' => 'update',
            'image_controller' => 'App\Http\Controllers\ImageController',
            'image_controller_method' => 'storeLocationImage',
            'dependent_attr' => array('id' => array(
                'location' => array(
                    'parent_id'
                ),
                'object' => array(
                    'location_id'
                ),
                'cable_path' => array(
                    'cabinet_a_id',
                    'cabinet_b_id'
                ),
            )),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'object' => array(
            'filename' => 'object.csv',
            'controller' => 'App\Http\Controllers\ObjectController',
            'model' => 'App\Models\ObjectModel',
            'post_method' => array(
                'standard' => 'storeStandard',
                'insert' => 'storeInsert',
                'floorplan' => 'storeFloorplan'
            ),
            'patch_method' => 'update',
            'image_controller' => false,
            'image_controller_method' => false,
            'dependent_attr' => array(
                'id' => array(
                    'object' => array(
                        'parent_id'
                    ),
                    'trunk' => array(
                        'a_id',
                        'b_id'
                    ),
                    'connection' => array(
                        'a_id',
                        'b_id'
                    ),
                    'port' => array(
                        'object_id'
                    )
                )
            ),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'trunk' => array(
            'filename' => 'trunk.csv',
            'controller' => 'App\Http\Controllers\TrunkController',
            'model' => 'App\Models\TrunkModel',
            'post_method' => 'store',
            'patch_method' => 'update',
            'image_controller' => false,
            'image_controller_method' => false,
            'dependent_attr' => array(),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'port' => array(
            'filename' => 'port.csv',
            'controller' => 'App\Http\Controllers\PortController',
            'model' => 'App\Models\PortModel',
            'post_method' => 'store',
            'patch_method' => 'store',
            'image_controller' => false,
            'image_controller_method' => false,
            'dependent_attr' => array(),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'cable' => array(
            'filename' => 'cable.csv',
            'controller' => 'App\Http\Controllers\CableController',
            'model' => 'App\Models\CableModel',
            'post_method' => 'store',
            'patch_method' => 'update',
            'image_controller' => false,
            'image_controller_method' => false,
            'dependent_attr' => array(),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'connection' => array(
            'filename' => 'connection.csv',
            'controller' => 'App\Http\Controllers\ConnectionController',
            'model' => 'App\Models\ConnectionModel',
            'post_method' => 'store',
            'patch_method' => 'store',
            'image_controller' => false,
            'image_controller_method' => false,
            'dependent_attr' => array(),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
        'cable_path' => array(
            'filename' => 'cable_path.csv',
            'controller' => 'App\Http\Controllers\CablePathController',
            'model' => 'App\Models\CablePathModel',
            'post_method' => 'store',
            'patch_method' => 'update',
            'image_controller' => false,
            'image_controller_method' => false,
            'dependent_attr' => array(),
            'dependency_mapping' => array(),
            'entryIDArray' => array()
        ),
    );

    protected $conversionMap = array(
        'category' => array(),
        'template' => array(),
        'location' => array(),
        'cable_path' => array(),
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

            // Callback functions
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

                // Create/update entries
                foreach($this->tableSchemas as $tableName => $tableSchema) {

                    $tableController = new $tableSchema['controller'];
                    $tableModel = new $tableSchema['model'];
                    $tableImgController = ($tableSchema['image_controller']) ? new $tableSchema['image_controller'] : false;
                    $tableFileName = $tableSchema['filename'];
                    $tableFilePath = Storage::disk('local')->path('imports/'.$tableFileName);
                    $file = fopen($tableFilePath, 'r');
                    $row = 1;
                    if($file !== FALSE) {
                        
                        while (($data = fgetcsv($file, 100000, ",")) !== FALSE) {

                            if($row == 1){

                                // Process header
                                $attrMap = $this->processCSVHeader($data);
                            } else {

                                // Process entry
                                $importIDArray = $this->processCSVEntry($data, $attrMap, $tableController, $tableModel, $tableImgController, $tableName, $tableFileName, $row);

                                // Add IDs to entry array
                                $this->tableSchemas[$tableName]['entryIDArray'] = array_merge($this->tableSchemas[$tableName]['entryIDArray'], $importIDArray);
                            }
                            $row++;
                        }
                        
                    } else {
                        throw ValidationException::withMessages(['error' => 'Not able to open extracted file.']);
                    }
                    fclose($file);
                }

                // Delete entries
                for($x=count($this->tableSchemas)-1; $x>1; $x--) {

                    $tableModel = new $tableSchema['model'];

                    // Find entries to delete
                    foreach($tableModel->all() as $dbEntry) {
                        $dbEntryID = $dbEntry['id'];
                        if(!in_array($dbEntryID, $this->tableSchemas[$tableName]['entryIDArray'])) {

                            // Set a reference to the archive for helpful error reporting
                            $tableController->archiveAddress = $tableFileName.":".$row;
                            
                            // Submit DELETE request
                            $newResponse = call_user_func(array($tableController, 'destroy'), $dbEntryID);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function convert(Request $request)
    {

        // RBAC
        if (! Gate::allows('operator')) {
            abort(403);
        }

        $PCM = new PCM;

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

        $headers = array(
            'Content-Type' => 'application/zip'
        );

        $manifest = array(
            '01 - Categories.csv',
            '02 - Templates.csv',
            '03 - Cabinets.csv',
            '04 - Cabinet Cable Paths.csv'
        );

        // Store legacy archive
        $legacyArchivePath = $request->file('file')->store('imports');

        // Get legacy file paths
        $legacyArchivePathArray = explode('/', $legacyArchivePath);
        $legacyArchiveFilename = end($legacyArchivePathArray);
        $legacyArchiveFilePath = Storage::disk('local')->path('imports/'.$legacyArchiveFilename);
        $legacyArchiveDestPath = Storage::disk('local')->path('imports');

        // Callback functions
        $tableEntryCallback = fn($row) => is_array($row) ? json_encode($row) : $row;

        // Extract legacy archive
        $zip = new \ZipArchive();
        if($zip->open($legacyArchiveFilePath) === TRUE) {
            
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
            
            // Extract legacy archive
            if(!$zip->extractTo($legacyArchiveDestPath, $manifest)) {
                throw ValidationException::withMessages(['error' => 'Not able to extract legacy archive.']);
            }
        }

        // Open ZIP
        $zip = new \ZipArchive();
        $zipFilename = 'archive.zip';
        $zipFilePath = Storage::disk('local')->path($zipFilename);
        if($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {

            $archiveSchemas = array(
                'category' => array(
                    'filename' => '01 - Categories.csv',
                    'attr_mapping' => array(
                        array(
                            'new' => 'id',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                $ID = count($this->conversionMap['category'])+1;
                                $this->conversionMap['category'][$data] = $ID;
                                return $ID;
                            }
                        ),
                        array(
                            'new' => 'name',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'color',
                            'old' => 'Color',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'default',
                            'old' => false,
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'visible',
                            'old' => false,
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                    )
                ),
                'template' => array(
                    'filename' => '02 - Templates.csv',
                    'attr_mapping' => array(
                        array(
                            'new' => 'id',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                $ID = count($this->conversionMap['category'])+1;
                                $this->conversionMap['category'][$data] = $ID;
                                return $ID;
                            }
                        ),
                        array(
                            'new' => 'name',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'category_id',
                            'old' => 'Category',
                            'process' => function($data=null) {
                                return $this->conversionMap['category'][$data];
                            }
                        ),
                        array(
                            'new' => 'type',
                            'old' => '**Type',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'function',
                            'old' => '**Function',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'ru_size',
                            'old' => '**RU Size',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'mount_config',
                            'old' => '**Mount Config',
                            'process' => function($data=null) {
                                if($data == 'N/A') {
                                    return null;
                                }
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'insert_constraints',
                            'old' => '**Template Structure',
                            'process' => function($data=null) {
                                $data = json_decode($data, true);
                                if($data != null) {

                                    $workingArray = array();
                                    if($data['sizeX'] && $data['sizeY'] && $data['parentH'] && $data['parentV']) {
                                        array_push($workingArray, array(
                                            'part_layout' => array(
                                                'height' => $data['parentV'],
                                                'width' => $data['parentH']
                                            ),
                                            'enc_layout' => array(
                                                'cols' => $data['sizeX'],
                                                'rows' => $data['sizeY']
                                            )
                                        ));
                                    } else {
                                        return null;
                                    }

                                    if($data['nestedSizeX'] && $data['nestedSizeY'] && $data['nestedParentH'] && $data['nestedParentV']) {
                                        array_push($workingArray, array(
                                            'part_layout' => array(
                                                'height' => $data['nestedParentV'],
                                                'width' => $data['nestedParentH']
                                            ),
                                            'enc_layout' => array(
                                                'cols' => $data['nestedSizeX'],
                                                'rows' => $data['nestedSizeY']
                                            )
                                        ));
                                    }
                                    return json_encode($workingArray);
                                } else {
                                    return null;
                                }
                            }
                        ),
                        array(
                            'new' => 'blueprint',
                            'old' => '**Template Structure',
                            'process' => function($data=null) {
                                $data = json_decode($data, true);
                                $structure = $data['structure'];
                                $blueprint = array();
                                if(isset($structure[0])) {
                                    $blueprint['front'] = $this->processPartition($structure[0]);
                                }
                                if(isset($structure[1])) {
                                    $blueprint['rear'] = $this->processPartition($structure[1]);
                                }
                                return json_encode($blueprint);
                            }
                        ),
                        array(
                            'new' => 'img_front',
                            'old' => '**Template Structure',
                            'process' => function($data=null) {
                                $data = json_decode($data, true);
                                return ($data) ? $data['frontImage'] : null;
                            }
                        ),
                        array(
                            'new' => 'img_rear',
                            'old' => '**Template Structure',
                            'process' => function($data=null) {
                                $data = json_decode($data, true);
                                return ($data) ? $data['rearImage'] : null;
                            }
                        ),
                    )
                ),
                'location' => array(
                    'filename' => '03 - Cabinets.csv',
                    'attr_mapping' => array(
                        array(
                            'new' => 'id',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                $ID = count($this->conversionMap['location'])+1;
                                $this->conversionMap['location'][$data] = $ID;
                                return $ID;
                            }
                        ),
                        array(
                            'new' => 'name',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                $nameArray = explode(".", $data);
                                $name = end($nameArray);
                                return $name;
                            }
                        ),
                        array(
                            'new' => 'type',
                            'old' => 'Type',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),
                        array(
                            'new' => 'parent_id',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                $nameArray = explode(".", $data);
                                array_pop($nameArray);
                                if(count($nameArray)) {
                                    return $this->conversionMap['location'][$data];
                                } else {
                                    return 0;
                                }
                            }
                        ),
                        array(
                            'new' => 'size',
                            'old' => 'RU Size',
                            'process' => function($data=null) {
                                return $data;
                            }
                        ),
                        array(
                            'new' => 'img',
                            'old' => '**Floorplan Image',
                            'process' => function($data=null) {
                                return $data;
                            }
                        ),
                        array(
                            'new' => 'ru_orientation',
                            'old' => 'RU Orientation',
                            'process' => function($data=null) {
                                if($data == 'BottomUp') {
                                    return 'bottom-up';
                                } else if($data == 'TopDown') {
                                    return 'top-down';
                                } else {
                                    return $data;
                                }
                            }
                        ),
                        array(
                            'new' => 'left_adj_cabinet_id',
                            'old' => 'Adj Left',
                            'process' => function($data=null) {
                                $nameArray = explode(".", $data);
                                array_pop($nameArray);
                                if(count($nameArray)) {
                                    return $this->conversionMap['location'][$data];
                                } else {
                                    return null;
                                }
                            }
                        ),
                        array(
                            'new' => 'right_adj_cabinet_id',
                            'old' => 'Adj Right',
                            'process' => function($data=null) {
                                $nameArray = explode(".", $data);
                                array_pop($nameArray);
                                if(count($nameArray)) {
                                    return $this->conversionMap['location'][$data];
                                } else {
                                    return null;
                                }
                            }
                        ),
                    )
                ),
                'cable_path' => array(
                    'filename' => '04 - Cabinet Cable Paths.csv',
                    'attr_mapping' => array(
                        array(
                            'new' => 'id',
                            'old' => array('Cabinet A', 'Cabinet B'),
                            'process' => function($data=null) {
                                $ID = count($this->conversionMap['cable_path'])+1;
                                $this->conversionMap['location'][$data[0].$data[1]] = $ID;
                                return $ID;
                            }
                        ),
                        array(
                            'new' => 'cabinet_a_id',
                            'old' => 'Cabinet A',
                            'process' => function($data=null) {
                                return $this->conversionMap['location'][$data];
                            }
                        ),
                        array(
                            'new' => 'cabinet_b_id',
                            'old' => 'Cabinet B',
                            'process' => function($data=null) {
                                return $this->conversionMap['location'][$data];
                            }
                        ),
                        array(
                            'new' => 'distance',
                            'old' => 'Distance (m.)',
                            'process' => function($data=null) {
                                return $data;
                            }
                        ),
                        array(
                            'new' => 'notes',
                            'old' => 'Notes',
                            'process' => function($data=null) {
                                return $data;
                            }
                        ),
                    )
                ),
            );

            // Create/update entries
            foreach($archiveSchemas as $tableName => $archiveSchema) {

                // Open temp stream to write CSV
                $csvFile = fopen('php://temp', 'w');

                // Open legacy archive file
                $archiveFilename = $archiveSchema['filename'];
                $archiveFilePath = Storage::disk('local')->path('imports/'.$archiveFilename);
                $file = fopen($archiveFilePath, 'r');
                $row = 1;
                if($file !== FALSE) {
                    
                    while (($data = fgetcsv($file, 100000, ",")) !== FALSE) {

                        if($row == 1){

                            // Process header
                            $attrMap = $this->processCSVHeader($data);
                            $workingArray = array();
                            foreach($archiveSchema['attr_mapping'] as $attrIdx => $attrMapping) {
                                array_push($workingArray, $attrMapping['new']);
                            }

                            // Add header to CSV file
                            fputcsv($csvFile, $workingArray);
                        } else {

                            // Process entry
                            $workingArray = array();
                            foreach($archiveSchema['attr_mapping'] as $attrIdx => $attrMapping) {

                                Log::info($tableName.':'.$row.' '.json_encode($attrMapping['old']));
                                // Initialize legacy data
                                $legacyData = null;

                                // Retrieve legacy data
                                if($attrMapping['old']) {
                                    if(is_array($attrMapping['old'])) {
                                        $legacyData = array();
                                        foreach($attrMapping['old'] as $attr) {
                                            array_push($legacyData, $data[$attrMap[$attr]]);
                                        }
                                    } else {
                                        $legacyData = $data[$attrMap[$attrMapping['old']]];
                                    }
                                }

                                // Process legacy data
                                $processedData = $attrMapping['process']($legacyData);

                                // Add processed data to row
                                array_push($workingArray, $processedData);
                            }

                            // Add row to CSV file
                            fputcsv($csvFile, $workingArray);
                        }
                        $row++;
                    }

                    // Store CSV to disk
                    $filename = $tableName.'.csv';
                    Storage::disk('local')->put($filename, $csvFile);
                    fclose($csvFile);

                    // Add CSV to ZIP
                    $zip->addFile(Storage::disk('local')->path($filename), $filename);
                    
                } else {
                    throw ValidationException::withMessages(['error' => 'Not able to open extracted file.']);
                }
                fclose($file);
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
     * process CSV Header by mapping header to index value
     *
     * @param   string $tableName
     * @param   array $data
     * @return  array
     */
    private function processCSVHeader($data)
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
     * @param   object $tableController
     * @param   object $tableModel
     * @param   object $tableImgController
     * @param   string $tableFileName
     * @param   integer $row
     * @return  integer
     */
    private function processCSVEntry($data, $attrMap, $tableController, $tableModel, $tableImgController, $tableName, $tableFileName, $row)
    {
        $tableSchema = $this->tableSchemas[$tableName];

        // Create request
        $importRequest = new Request;

        // Get entry ID
        $entryID = $data[$attrMap['id']];

        // Determine if entry should be created or updated
        if($entryID) {
            if($original = $tableModel->where('id', '=', $entryID)->first()) {

                // Update existing entry
                $entryAction = 'update';

                // Create new entry if static attributes do not match
                foreach($this->staticAttributes[$tableName] as $attr) {
                    if($data[$attrMap[$attr]] != $original[$attr]) {
                        $entryAction = 'create';
                    }
                }
            } else {
                $entryAction = 'create';
            }
        } else {
            $entryAction = 'create';
        }

        // Prepare request
        switch($entryAction) {

            case 'create':
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
                $importRequest->setMethod('PATCH');
                $controllerMethod = $tableSchema['patch_method'];
                break;
        }

        // Compile request data
        $requestData = array();
        foreach(array_keys($attrMap) as $attr) {

            // Get array index of attribute
            $attrIdx = $attrMap[$attr];

            // Apply attribute value mapping
            if(isset($tableSchema['dependency_mapping'][$attr])) {
                if(isset($tableSchema['dependency_mapping'][$attr][$data[$attrIdx]])) {
                    $data[$attrIdx] = $tableSchema['dependency_mapping'][$attr][$data[$attrIdx]];
                }
            }

            // Decode JSON if necessary
            $requestDataEntry = (in_array($attr, $this->jsonAttributes[$tableName])) ? json_decode($data[$attrIdx], true) : $data[$attrIdx];

            // Add attribute to request data
            $requestData[$attr] = $requestDataEntry;
        }

        // Set a reference to the archive for helpful error reporting
        $tableController->archiveAddress = $tableFileName.":".$row;

        // Submit request
        $importRequest->request->add($requestData);
        $importResponse = call_user_func(array($tableController, $controllerMethod), $importRequest, $entryID);
        
        // Store dependency mapping
        foreach($tableSchema['dependent_attr'] as $attr => $dependentTables) {

            // Collect mapping data
            $old = $data[$attrMap[$attr]];
            $new = (isset($importResponse['add'])) ? $importResponse['add'][$attr] : $importResponse[$attr];

            foreach($dependentTables as $dependentTableName => $dependentTableAttrs) {
                foreach($dependentTableAttrs as $dependentTableAttr) {

                    // Create dependency mapping attribute array if it doesn't exist
                    if(!isset($this->tableSchemas[$dependentTableName]['dependency_mapping'][$dependentTableAttr])) {
                        $this->tableSchemas[$dependentTableName]['dependency_mapping'][$dependentTableAttr] = array();
                    }

                    // Add mapping
                    $this->tableSchemas[$dependentTableName]['dependency_mapping'][$dependentTableAttr][$old] = $new;
                }
            }
        }

        // Store added row IDs
        $importIDArray = array();
        if(isset($importResponse['add'])) {
            array_push($importIDArray, $importResponse['add']['id']);
        } else {
            array_push($importIDArray, $importResponse['id']);
        }

        // Process images
        foreach($this->imgAttributes[$tableName] as $imgAttr) {

            // Get image file name
            $attrIdx = $attrMap[$imgAttr];
            $imgFileName = $data[$attrIdx];

            if($imgFileName) {

                // Get image file path
                $imgFilePath = Storage::disk('local')->path('imports/images/'.$imgFileName);

                // Determine if image file is the same
                $fileIsSame = false;
                $imageEntry = $tableModel->where('id', '=', $importIDArray[0])->first();
                if($imageEntry[$imgAttr]) {
                    $origImgFilePath = Storage::disk('local')->path('images/'.$imageEntry[$imgAttr]);
                    if(file_exists($origImgFilePath)) {
                        if(md5_file($origImgFilePath) == md5_file($imgFilePath)) {
                            $fileIsSame = true;
                        }
                    }
                }

                if(!$fileIsSame) {

                    $requestData = array();
                    if(strpos($imgAttr, 'front') !== false) {
                        $requestData['face'] = 'front';
                    } else if(strpos($imgAttr, 'rear') !== false) {
                        $requestData['face'] = 'rear';
                    }

                    $imgMimeType = Storage::mimeType('imports/images/'.$imgFileName);
                    $imgFile = array('file' => new UploadedFile($imgFilePath, $imgFileName, $imgMimeType, null, true));
                    $imgRequest = (new Request())->duplicate($requestData, [], [], [], $imgFile);

                    $imageResponse = call_user_func(array($tableImgController, $tableSchema['image_controller_method']), $imgRequest, $importIDArray[0]);
                }
            }
        }

        return $importIDArray;
    }

    /**
     * process partition data
     *
     * @param   array $data
     * @return  array
     */
    private function processPartition($data, $depth=0)
    {
        $workingArray = array();
        foreach($data as $partition) {

            $partitionType = strtolower($partition['partitionType']);
            $partitionDirection = $partition['direction'];
            
            if($depth == 0 && $partitionDirection == 'row') {

                // First partition is "row" so add generic 1st partition and proceed
                $workingArray['type'] = 'generic';
                $workingArray['units'] = 24;
                $workingArray['children'] = array();
                $depth++;
                array_push($workingArray['children'], $this->processPartition($data, $depth));

            } else {

                $workingArray['type'] = $partitionType;
                $workingArray['units'] = ($partitionDirection == 'row') ? $partition['vUnits'] : $partition['hUnits'];
                $workingArray['children'] = array();


                switch($partitionType) {
                    case 'generic':
                        $depth++;
                        if(isset($partition['children'])) {
                            array_push($workingArray['children'], $this->processPartition($partition['children'], $depth));
                        }
                        break;

                    case 'connectable':
                        $workingArray['port_format'] = $partition['portNameFormat'];
                        $workingArray['media'] = $partition['mediaType'];
                        $workingArray['port_connector'] = $partition['portType'];
                        $workingArray['port_orientation'] = $partition['portOrientation'];
                        break;

                    case 'enclosure':
                        $workingArray['enc_layout'] = array(
                            'cols' => $partition['valueX'],
                            'rows' => $partition['valueY']
                        );
                        break;
                }
            }
        }

        return $workingArray;
    }
}
