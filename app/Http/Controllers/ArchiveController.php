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
        'object' => array(),
        'cable' => array(),
        'connection' => array(),
        'trunk' => array(),
    );

    protected $templateArray = [
        'Walljack' => [
            'id' => 'walljack',
            'name' => 'walljack',
            'category_id' => 'walljack',
            'type' => 'floorplan',
            'function' => 'passive',
            'mount_config' => 'N/A',
            'insert_constraints' => null,
            'blueprint' => [
                'front' => [
                    [
                        'type' => 'connectable',
                        'units' => 24,
                        'children' => [],
                        'port_format' => [
                            [
                                'type' => 'static',
                                'value' => 'Port',
                                'count' => 1,
                                'order' => 0
                            ],
                            [
                                'type' => 'incremental',
                                'value' => '1',
                                'count' => 48,
                                'order' => 1
                            ]
                        ],
                        'port_layout' => [
                            'cols' => 24,
                            'rows' => 1
                        ],
                        'media' => 1,
                        'port_connector' => 1,
                        'port_orientation' => 1
                    ]
                ],
                'rear' => [
                    [
                        'type' => 'generic',
                        'units' => 24,
                        'children' => []
                    ]
                ]
            ]
        ],
        'Device' => [
            'id' => 'device',
            'name' => 'device',
            'category_id' => 'device',
            'type' => 'floorplan',
            'function' => 'endpoint',
            'mount_config' => 'N/A',
            'insert_constraints' => null,
            'blueprint' => [
                'front' => [
                    [
                        'type' => 'connectable',
                        'units' => 24,
                        'children' => [],
                        'port_format' => [
                            [
                                'type' => 'static',
                                'value' => 'Port',
                                'count' => 1,
                                'order' => 0
                            ],
                            [
                                'type' => 'incremental',
                                'value' => '1',
                                'count' => 48,
                                'order' => 1
                            ]
                        ],
                        'port_layout' => [
                            'cols' => 24,
                            'rows' => 1
                        ],
                        'media' => 1,
                        'port_connector' => 1,
                        'port_orientation' => 1
                    ]
                ],
                'rear' => [
                    [
                        'type' => 'generic',
                        'units' => 24,
                        'children' => []
                    ]
                ]
            ]
        ],
        'WAP' => [
            'id' => 'wap',
            'name' => 'wap',
            'category_id' => 'wap',
            'type' => 'floorplan',
            'function' => 'endpoint',
            'mount_config' => 'N/A',
            'insert_constraints' => null,
            'blueprint' => [
                'front' => [
                    [
                        'type' => 'connectable',
                        'units' => 24,
                        'children' => [],
                        'port_format' => [
                            [
                                'type' => 'static',
                                'value' => 'Port',
                                'count' => 1,
                                'order' => 0
                            ],
                            [
                                'type' => 'incremental',
                                'value' => '1',
                                'count' => 48,
                                'order' => 1
                            ]
                        ],
                        'port_layout' => [
                            'cols' => 24,
                            'rows' => 1
                        ],
                        'media' => 1,
                        'port_connector' => 1,
                        'port_orientation' => 1
                    ]
                ],
                'rear' => [
                    [
                        'type' => 'generic',
                        'units' => 24,
                        'children' => []
                    ]
                ]
            ]
        ],
        'Camera' => [
            'id' => 'camera',
            'name' => 'camera',
            'category_id' => 'camera',
            'type' => 'floorplan',
            'function' => 'endpoint',
            'mount_config' => 'N/A',
            'insert_constraints' => null,
            'blueprint' => [
                'front' => [
                    [
                        'type' => 'connectable',
                        'units' => 24,
                        'children' => [],
                        'port_format' => [
                            [
                                'type' => 'static',
                                'value' => 'Port',
                                'count' => 1,
                                'order' => 0
                            ],
                            [
                                'type' => 'incremental',
                                'value' => '1',
                                'count' => 48,
                                'order' => 1
                            ]
                        ],
                        'port_layout' => [
                            'cols' => 24,
                            'rows' => 1
                        ],
                        'media' => 1,
                        'port_connector' => 1,
                        'port_orientation' => 1
                    ]
                ],
                'rear' => [
                    [
                        'type' => 'generic',
                        'units' => 24,
                        'children' => []
                    ]
                ]
            ]
        ]
    ];

    protected $locationArray = array();
    
    protected $objectArray = array();

    protected $properToActualMapping = array();

    protected $conversionEntryPasses = true;

    protected $conversionArchiveAddress = '';

    protected $partitionPrepended = array();

    protected $trunkGroupIDArray = array();

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
            '04 - Cabinet Cable Paths.csv',
            '05 - Cabinet Objects.csv',
            '06 - Object Inserts.csv',
            '07 - Connections.csv',
            '08 - Trunks.csv'
        );

        $imgFileArray = array();

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
                if(preg_match("/^(templateImages|floorplanImages)\/[a-zA-Z0-9]{32}\.(jpg|png|gif)$/", $zippedFilename)) {
                    array_push($manifest, $zippedFilename);

                    // extract image filename
                    $imgFilenameArray = explode('/', $zippedFilename);
                    $imgFilenameOld = array_pop($imgFilenameArray);

                    // Generate new filename
                    $imgFilenameExtensionArray = explode('.', $imgFilenameOld);
                    $imgFilenameExtension = array_pop($imgFilenameExtensionArray);
                    $imgFilenameNew = $this->generateFilename().'.'.$imgFilenameExtension;

                    // append image filename to image file array
                    $imgFileArray[$imgFilenameOld] = array(
                        'path' => $zippedFilename,
                        'name' => $imgFilenameNew
                    );
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

            $conversionMapping = array(
                'category' => array(
                    '01 - Categories.csv' => array(
                        'fields' => array('Name'),
                        'process' => function($data) {
                            $conversionMapHash = implode(".", $data);
                            $ID = 'C-'.count($this->conversionMap['category'])+1;
                            $this->conversionMap['category'][$conversionMapHash] = $ID;
                            return;
                        }
                    )
                ),
                'template' => array(
                    '02 - Templates.csv' => array(
                        'fields' => array('Name'),
                        'process' => function($data) {
                            $conversionMapHash = implode(".", $data);
                            $ID = 'C-'.count($this->conversionMap['template'])+1;
                            $this->conversionMap['template'][$conversionMapHash] = $ID;
                            return;
                        }
                    )
                ),
                'location' => array(
                    '03 - Cabinets.csv' => array(
                        'fields' => array('Name'),
                        'process' => function($data) {
                            $conversionMapHash = implode(".", $data);
                            $ID = 'C-'.count($this->conversionMap['location'])+1;
                            $this->conversionMap['location'][$conversionMapHash] = $ID;
                            return;
                        }
                    )
                ),
                'cable_path' => array(
                    '04 - Cabinet Cable Paths.csv' => array(
                        'fields' => array('Cabinet A', 'Cabinet B'),
                        'process' => function($data) {
                            $conversionMapHash = implode("-", $data);
                            $ID = 'C-'.count($this->conversionMap['cable_path'])+1;
                            $this->conversionMap['cable_path'][$conversionMapHash] = $ID;
                            return;
                        }
                    )
                ),
                'object' => array(
                    '05 - Cabinet Objects.csv' => array(
                        'fields' => array('Cabinet', 'Name'),
                        'process' => function($data) {
                            $conversionMapHash = implode(".", $data);
                            $ID = 'C-'.count($this->conversionMap['object'])+1;
                            $this->conversionMap['object'][$conversionMapHash] = $ID;
                            return;
                        }
                    ),
                    '06 - Object Inserts.csv' => array(
                        'fields' => array('**Object', 'Insert Name'),
                        'process' => function($data) {

                            $parentDN = $data[0];
                            $objectName = $data[1];

                            // Skip if name is blank
                            if($objectName !== '') {

                                $ID = 'C-'.count($this->conversionMap['object'])+1;

                                $objectDNArray = array($parentDN, $objectName);
                                $objectDN = implode('.', $objectDNArray);

                                $this->conversionMap['object'][$objectDN] = $ID;
                                return;
                            }
                        }
                    )
                ),
                'cable' => array(
                    '07 - Connections.csv' => array(
                        'fields' => array('CableA ID', 'CableB ID'),
                        'process' => function($data) {
                            $conversionMapHash = implode(".", $data);
                            $ID = 'C-'.count($this->conversionMap['cable'])+1;
                            $this->conversionMap['cable'][$conversionMapHash] = $ID;
                            return;
                        }
                    )
                ),
                'connection' => array(
                    '07 - Connections.csv' => array(
                        'fields' => array('CableA ID', 'CableB ID'),
                        'process' => function($data) {
                            $conversionMapHash = implode(".", $data);
                            $ID = 'C-'.count($this->conversionMap['connection'])+1;
                            $this->conversionMap['connection'][$conversionMapHash] = $ID;
                            return;
                        }
                    )
                ),
                'trunk' => array(
                    '08 - Trunks.csv' => array(
                        'fields' => array('Trunk Peer A', 'Trunk Peer B'),
                        'process' => function($data) {
                            $conversionMapHash = implode(".", $data);
                            $ID = 'C-'.count($this->conversionMap['trunk'])+1;
                            $this->conversionMap['trunk'][$conversionMapHash] = $ID;
                            return;
                        }
                    )
                ),
                'port' => array(
                    '07 - Connections.csv' => array(
                        'fields' => array('PortA'),
                        'process' => function($data) {
                            return;
                        }
                    )
                ),
            );

            $archiveSchemas = array(
                'category' => array(
                    '01 - Categories.csv' => array(
                        array(
                            'new' => 'id',
                            'old' => 'Name',
                            'process' => function($data) {
                                return $this->conversionMap['category'][$data];
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
                            'process' => function($data) {
                                $colorCode = strtoupper($data.'FF');
                                return $colorCode;
                            }
                        ),
                        array(
                            'new' => 'default',
                            'old' => false,
                            'process' => function($data) {
                                return ($data) ? 1 : 0;
                            }
                        ),
                        array(
                            'new' => 'visible',
                            'old' => false,
                            'process' => function($data) {
                                return 1;
                            }
                        ),
                    )
                ),
                'template' => array(
                    '02 - Templates.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => 'Name',
                            'process' => function($data) {
                                return $this->conversionMap['template'][$data];
                            }
                        ),

                        // name
                        array(
                            'new' => 'name',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),

                        // category_id
                        array(
                            'new' => 'category_id',
                            'old' => 'Category',
                            'process' => function($data=null) {
                                return $this->conversionMap['category'][$data];
                            }
                        ),

                        // type
                        array(
                            'new' => 'type',
                            'old' => array('Name', '**Type'),
                            'process' => function($data) {

                                $templateName = $data[0];
                                $templateType = strtolower($data[1]);

                                $this->templateArray[$templateName]['type'] = $templateType;

                                return $templateType;
                            }
                        ),

                        // function
                        array(
                            'new' => 'function',
                            'old' => array('Name', '**Function'),
                            'process' => function($data) {
                                
                                $templateName = $data[0];
                                $templateFunction = strtolower($data[1]);

                                $this->templateArray[$templateName]['function'] = $templateFunction;

                                return $templateFunction;
                            }
                        ),

                        // ru_size
                        array(
                            'new' => 'ru_size',
                            'old' => array('Name', '**RU Size'),
                            'process' => function($data) {
                                $templateName = $data[0];
                                $RUSize = $data[1];
                                $this->templateArray[$templateName]['ru_size'] = $RUSize;
                                return ($RUSize) ? $RUSize : null;
                            }
                        ),

                        // mount_config
                        array(
                            'new' => 'mount_config',
                            'old' => '**Mount Config',
                            'process' => function($data) {
                                if($data == 'N/A') {
                                    return null;
                                } else {
                                    return strtolower($data);
                                }
                            }
                        ),

                        // insert_constraints
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

                        // blueprint
                        array(
                            'new' => 'blueprint',
                            'old' => array('Name', '**Template Structure'),
                            'process' => function($data=null) {
                                $templateName = $data[0];
                                $templateStructure = json_decode($data[1], true);
                                $structure = $templateStructure['structure'];
                                $blueprint = array();
                                if(isset($structure[0])) {
                                    $blueprint['front'] = $this->processPartition($structure[0]);
                                }
                                if(isset($structure[1])) {
                                    $blueprint['rear'] = $this->processPartition($structure[1]);
                                } else {
                                    $blueprint['rear'] = array(
                                        array(
                                            'type' => 'generic',
                                            'units' => 24,
                                            'children' => array()
                                        )
                                    );
                                }

                                // Store template blueprint
                                $this->templateArray[$templateName]['blueprint'] = $blueprint;

                                return json_encode($blueprint);
                            }
                        ),

                        // img_front
                        array(
                            'new' => 'img_front',
                            'old' => '**Template Structure',
                            'process' => function($data) {
                                $data = json_decode($data, true);
                                if($data['frontImage']) {
                                    $imgFilename = $data['frontImage'];
                                    if(isset($imgFileArray[$imgFilename])) {
                                        return $imgFileArray[$imgFilename]['name'];
                                    }
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // img_rear
                        array(
                            'new' => 'img_rear',
                            'old' => '**Template Structure',
                            'process' => function($data) {
                                $data = json_decode($data, true);
                                if($data['frontImage']) {
                                    $imgFilename = $data['frontImage'];
                                    if(isset($imgFileArray[$imgFilename])) {
                                        return $imgFileArray[$imgFilename]['name'];
                                    }
                                } else {
                                    return null;
                                }
                            }
                        ),
                    )
                ),
                'location' => array(
                    '03 - Cabinets.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => 'Name',
                            'process' => function($data) {
                                $this->locationArray[$data] = array();
                                return $this->conversionMap['location'][$data];
                            }
                        ),

                        // name
                        array(
                            'new' => 'name',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                $nameArray = explode(".", $data);
                                $name = end($nameArray);
                                return $name;
                            }
                        ),

                        // type
                        array(
                            'new' => 'type',
                            'old' => 'Type',
                            'process' => function($data=null) {
                                return ($data) ? $data : null;
                            }
                        ),

                        // parent_id
                        array(
                            'new' => 'parent_id',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                $nameArray = explode(".", $data);
                                array_pop($nameArray);
                                if(count($nameArray)) {
                                    $conversionMapHash = implode(".", $nameArray);
                                    return $this->conversionMap['location'][$conversionMapHash];
                                } else {
                                    return 0;
                                }
                            }
                        ),

                        // size
                        array(
                            'new' => 'size',
                            'old' => 'RU Size',
                            'process' => function($data=null) {
                                return $data;
                            }
                        ),

                        // img
                        array(
                            'new' => 'img',
                            'old' => '**Floorplan Image',
                            'process' => function($data) {
                                if($data) {
                                    if(isset($imgFileArray[$data])) {
                                        return $imgFileArray[$data]['name'];
                                    }
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // ru_orientation
                        array(
                            'new' => 'ru_orientation',
                            'old' => array('Name', 'RU Orientation'),
                            'process' => function($data) {

                                $locationName = $data[0];
                                $locationRUOrientation = $data[1];

                                if($locationRUOrientation == 'BottomUp') {
                                    $ru_orientation = 'bottom-up';
                                } else if($locationRUOrientation == 'TopDown') {
                                    $ru_orientation = 'top-down';
                                } else {
                                    $ru_orientation = $locationRUOrientation;
                                }

                                $this->locationArray[$locationName]['ru_orientation'] = $ru_orientation;
                                return $ru_orientation;
                            }
                        ),

                        // left_adj_cabinet_id
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

                        // right_adj_cabinet_id
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
                    '04 - Cabinet Cable Paths.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => array('Cabinet A', 'Cabinet B'),
                            'process' => function($data) {
                                $conversionMapHash = implode("-", $data);
                                return $this->conversionMap['cable_path'][$conversionMapHash];
                            }
                        ),

                        // cabinet_a_id
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
                'object' => array(
                    '05 - Cabinet Objects.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => array('Cabinet', 'Name'),
                            'process' => function($data=null) {

                                $conversionMapHash = implode(".", $data);
                                $this->objectArray[$conversionMapHash] = array();
                                return $this->conversionMap['object'][$conversionMapHash];
                            }
                        ),

                        // name
                        array(
                            'new' => 'name',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return $data;
                            }
                        ),

                        // template_id
                        array(
                            'new' => 'template_id',
                            'old' => array('Name', 'Cabinet', '**Template'),
                            'process' => function($data=null) {
                                $objectName = $data[0];
                                $cabinetName = $data[1];
                                $templateName = $data[2];
                                $floorplanObjectArray = array(
                                    'Walljack',
                                    'Device',
                                    'WAP',
                                    'Camera'
                                );

                                $objectDN = $cabinetName.'.'.$objectName;

                                if(in_array($templateName, $floorplanObjectArray)) {

                                    // Store object template_name
                                    $this->objectArray[$objectDN]['template_name'] = $templateName;

                                    return null;
                                } else {

                                    // Store object template_name
                                    $this->objectArray[$objectDN]['template_name'] = $templateName;
                                    
                                    return $this->conversionMap['template'][$templateName];
                                }
                            }
                        ),

                        // location_id
                        array(
                            'new' => 'location_id',
                            'old' => 'Cabinet',
                            'process' => function($data=null) {
                                return $this->conversionMap['location'][$data];
                            }
                        ),

                        // cabinet_ru
                        array(
                            'new' => 'cabinet_ru',
                            'old' => array('Cabinet', '**Template', 'RU'),
                            'process' => function($data) {
                                $cabinetName = $data[0];
                                $templateName = $data[1];
                                $cabinetRU = $data[2];

                                if($cabinetRU) {

                                    // Get cabinet ru_orientation
                                    $location = $this->locationArray[$cabinetName];
                                    $locationRUOrientation = $location['ru_orientation'];

                                    // Get template ru_size
                                    $template = $this->templateArray[$templateName];
                                    $RUSize = $template['ru_size'];

                                    if($locationRUOrientation == 'bottom-up') {
                                        $cabinetRU = $cabinetRU + ($RUSize - 1);
                                    } else {
                                        $cabinetRU = $cabinetRU - ($RUSize - 1);
                                    }
                                }
                                return $cabinetRU;
                            }
                        ),

                        // cabinet_front
                        array(
                            'new' => 'cabinet_front',
                            'old' => 'Cabinet Face',
                            'process' => function($data=null) {
                                return strtolower($data);
                            }
                        ),

                        // parent_id
                        array(
                            'new' => 'parent_id',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // parent_face
                        array(
                            'new' => 'parent_face',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // parent_partition_address
                        array(
                            'new' => 'parent_partition_address',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // parent_enclosure_address
                        array(
                            'new' => 'parent_enclosure_address',
                            'old' => 'Name',
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // floorplan_address
                        array(
                            'new' => 'floorplan_address',
                            'old' => array('**Flooplan Object X', '**Flooplan Object Y'),
                            'process' => function($data=null) {
                                if($data[0] && $data[1]) {
                                    return json_encode($data);
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // floorplan_object_type
                        array(
                            'new' => 'floorplan_object_type',
                            'old' => array('Cabinet', 'Name', '**Template'),
                            'process' => function($data=null) {

                                $cabinetName = $data[0];
                                $objectName = $data[1];
                                $templateName = $data[2];
                                $objectDN = implode(".", array($cabinetName, $objectName));

                                $floorplanObjectArray = array(
                                    'Walljack' => 'walljack',
                                    'Device' => 'device',
                                    'WAP' => 'wap',
                                    'Camera' => 'camera'
                                );
                                if(isset($floorplanObjectArray[$templateName])) {
                                    $this->objectArray[$objectDN]['floorplan_object_type'] = $floorplanObjectArray[$templateName];
                                    return $floorplanObjectArray[$templateName];
                                } else {
                                    $this->objectArray[$objectDN]['floorplan_object_type'] = null;
                                    return null;
                                }
                            }
                        ),
                    ),
                    '06 - Object Inserts.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => array('**Object', 'Insert Name'),
                            'process' => function($data) {

                                $parentDN = $data[0];
                                $objectName = $data[1];

                                // Skip if name is blank
                                if($objectName == '') {
                                    $this->conversionEntryPasses = false;
                                    return null;
                                }

                                // Get object DN proper separator
                                $parent = $this->objectArray[$parentDN];
                                $parentTemplateName = $parent['template_name'];
                                $parentTemplate = $this->templateArray[$parentTemplateName];
                                $parentTemplateType = strtolower($parentTemplate['type']);
                                $parentTemplateFunction = strtolower($parentTemplate['function']);
                                $separator = ($parentTemplateFunction == 'passive' || $parentTemplateType == 'standard') ? '.' : '';

                                $objectDNArray = array($parentDN, $objectName);
                                $objectDNProper = implode($separator, $objectDNArray);
                                $objectDN = implode('.', $objectDNArray);

                                $this->objectArray[$objectDN] = array();
                                $this->properToActualMapping[$objectDNProper] = $objectDN;

                                return $this->conversionMap['object'][$objectDN];
                            }
                        ),

                        // name
                        array(
                            'new' => 'name',
                            'old' => 'Insert Name',
                            'process' => function($data=null) {
                                return $data;
                            }
                        ),

                        // template_id
                        array(
                            'new' => 'template_id',
                            'old' => array('**Object', 'Insert Name', '**Insert Template'),
                            'process' => function($data) {

                                $parentDN = $data[0];
                                $objectName = $data[1];
                                $templateName = $data[2];

                                if($templateName != '') {

                                    // Get object DN
                                    $objectDNArray = array($parentDN, $objectName);
                                    $objectDN = implode('.', $objectDNArray);

                                    $this->objectArray[$objectDN]['template_name'] = $templateName;
                                    return $this->conversionMap['template'][$templateName];
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // location_id
                        array(
                            'new' => 'location_id',
                            'old' => 'Insert Name',
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // cabinet_ru
                        array(
                            'new' => 'cabinet_ru',
                            'old' => 'Insert Name',
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // cabinet_front
                        array(
                            'new' => 'cabinet_front',
                            'old' => 'Insert Name',
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // parent_id
                        array(
                            'new' => 'parent_id',
                            'old' => '**Object',
                            'process' => function($data=null) {
                                return $this->conversionMap['object'][$data];
                            }
                        ),

                        // parent_face
                        array(
                            'new' => 'parent_face',
                            'old' => '**Face',
                            'process' => function($data=null) {
                                return strtolower($data);
                            }
                        ),

                        // parent_partition_address
                        array(
                            'new' => 'parent_partition_address',
                            'old' => array('**Object', '**Face', '**Slot'),
                            'process' => function($data=null) {

                                $parentObject = $data[0];
                                $parentFace = strtolower($data[1]);
                                $slot = $data[2];

                                if($slot) {
                                    
                                    // Get partition depth
                                    $slotComponents = preg_split('/[A-Z][a-z]+/', $slot);
                                    array_shift($slotComponents);
                                    $partitionDepth = intval($slotComponents[0]);

                                    // Get parent template blueprint
                                    $parentTemplateName = $this->objectArray[$parentObject]['template_name'];
                                    $template = $this->templateArray[$parentTemplateName];
                                    $blueprint = $template['blueprint'][$parentFace];

                                    // Increase partition depth to account for prepended partition
                                    if(isset($blueprint[0]['prepended'])) {
                                        $partitionDepth++;
                                    }

                                    // Get partition address
                                    $partitionAddress = $this->getPartitionAddress($blueprint, $partitionDepth);

                                    return json_encode($partitionAddress);
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // parent_enclosure_address
                        array(
                            'new' => 'parent_enclosure_address',
                            'old' => array('**Slot'),
                            'process' => function($data) {

                                $slot = $data[0];

                                if($slot) {

                                    $charArray = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
                                    
                                    // Extract original enclosure address
                                    $slotComponents = preg_split('/[A-Z][a-z]+/', $slot);
                                    array_shift($slotComponents);
                                    $enclosureAddress_orig = $slotComponents[1];

                                    // Extract enclosure address components
                                    preg_match('/[A-Z]+/', $enclosureAddress_orig, $row_orig);
                                    preg_match('/[0-9]+/', $enclosureAddress_orig, $col_orig);

                                    // Prepare enclosure address components
                                    $charIdx = 0;
                                    foreach($charArray as $char) {
                                        if($row_orig[0] == $char) {
                                            break;
                                        }
                                        $charIdx++;
                                    }
                                    //$row = array_search($row_orig[0], $charArray);
                                    $row = $charIdx;
                                    $col = intval($col_orig[0]) - 1;

                                    return json_encode(array($row, $col));
                                }
                            }
                        ),

                        // floorplan_address
                        array(
                            'new' => 'floorplan_address',
                            'old' => array('Insert Name'),
                            'process' => function($data) {
                                return null;
                            }
                        ),

                        // floorplan_object_type
                        array(
                            'new' => 'floorplan_object_type',
                            'old' => array('**Object', 'Insert Name'),
                            'process' => function($data) {

                                $parentDN = $data[0];
                                $objectName = $data[1];

                                $objectDN = implode('.', array($parentDN, $objectName));

                                $this->objectArray[$objectDN]['floorplan_object_type'] = null;
                                return null;
                            }
                        )
                    )
                ),
                'cable' => array(
                    '07 - Connections.csv' => array(
                        array(
                            'new' => 'id',
                            'old' => array('CableA ID', 'CableB ID'),
                            'process' => function($data) {
                                if($data[0] == 'None' && $data[1] = 'None') {
                                    $this->conversionEntryPasses = false;
                                }

                                $conversionMapHash = implode(".", $data);
                                return $this->conversionMap['cable'][$conversionMapHash];
                            }
                        ),
                        array(
                            'new' => 'a_id',
                            'old' => 'CableA ID',
                            'process' => function($data=null) {
                                $cableID = ($data == null or $data == 'None') ? null : $data;

                                return $cableID;
                            }
                        ),
                        array(
                            'new' => 'a_connector_id',
                            'old' => 'CableA Connector Type',
                            'process' => function($data=null) {

                                $cableConnectorArray = array(
                                    'RJ45' => 1,
                                    'LC' => 2,
                                    'SC' => 3,
                                    'MPO-12' => 5,
                                    'MPO-24' => 6,
                                    'Unspecified' => 7
                                );
                                $connectorID = (isset($cableConnectorArray[$data])) ? $cableConnectorArray[$data] : null;

                                return $connectorID;
                            }
                        ),
                        array(
                            'new' => 'b_id',
                            'old' => 'CableB ID',
                            'process' => function($data=null) {
                                $cableID = ($data == null or $data == 'None') ? null : $data;

                                return $cableID;
                            }
                        ),
                        array(
                            'new' => 'b_connector_id',
                            'old' => 'CableB Connector Type',
                            'process' => function($data=null) {

                                $cableConnectorArray = array(
                                    'RJ45' => 1,
                                    'LC' => 2,
                                    'SC' => 3,
                                    'MPO-12' => 5,
                                    'MPO-24' => 6,
                                    'Unspecified' => 7
                                );
                                $connectorID = (isset($cableConnectorArray[$data])) ? $cableConnectorArray[$data] : null;

                                return $connectorID;
                            }
                        ),
                        array(
                            'new' => 'media_id',
                            'old' => 'Media Type',
                            'process' => function($data=null) {

                                $mediaArray = array(
                                    'Cat5e' => 1,
                                    'Cat6' => 2,
                                    'Cat6a' => 3,
                                    'SM-OS1' => 5,
                                    'MM-OM4' => 6,
                                    'MM-OM3' => 7,
                                    'Unspecified' => 8,
                                );
                                $mediaID = (isset($mediaArray[$data])) ? $mediaArray[$data] : null;

                                return $mediaID;
                            }
                        ),
                        array(
                            'new' => 'length',
                            'old' => 'Length',
                            'process' => function($data=null) {

                                if($data == 'None') {
                                    return null;
                                } else {
                                    $lengthArray = explode(' ', $data);

                                    if(count($lengthArray) != 2) {
                                        return null;
                                    }

                                    if(!is_numeric($lengthArray[0])) {
                                        return null;
                                    }

                                    if(!in_array($lengthArray[1], array('m.', 'ft.'))) {
                                        return null;
                                    }

                                    if($lengthArray[1] == 'm.') {
                                        return intval($lengthArray[0]) * 100;
                                    } else if($lengthArray[1] == 'ft.') {
                                        return intval($lengthArray[0]) * 30.48;
                                    } else {
                                        return null;
                                    }
                                }

                                return $connectorID;
                            }
                        ),
                    )
                ),
                'connection' => array(
                    '07 - Connections.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => array('PortA', 'PortB', 'CableA ID', 'CableB ID'),
                            'process' => function($data) {

                                $portA = $data[0];
                                $portB = $data[1];
                                $cableAID = $data[2];
                                $cableBID = $data[3];

                                if(strtolower($portA) == 'none' && strtolower($portB) == 'none') {
                                    $this->conversionEntryPasses = false;
                                }

                                $conversionMapHash = implode(".", array($cableAID, $cableBID));
                                return $this->conversionMap['connection'][$conversionMapHash];
                            }
                        ),

                        // a_id
                        array(
                            'new' => 'a_id',
                            'old' => 'PortA',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {
                                    return $this->conversionMap['object'][$objectDN];
                                } else {
                                    throw ValidationException::withMessages(['error' => 'Not able to resolve PortA object. '.$this->conversionArchiveAddress]);
                                    return null;
                                }
                            }
                        ),

                        // a_face
                        array(
                            'new' => 'a_face',
                            'old' => 'PortA',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    $portData = $this->resolvePortDN($objectDN, $portDN);
                                    if(isset($portData[$portName])) {
                                        return $portData[$portName]['face'];
                                    } else {
                                        throw ValidationException::withMessages(['error' => 'Not able to resolve port in PortA column. '.$this->conversionArchiveAddress]);
                                        return 'Port not found';
                                    }
                                } else {
                                    throw ValidationException::withMessages(['error' => 'Not able to resolve object in PortA column. '.$this->conversionArchiveAddress]);
                                    return 'Object not found';
                                }
                            }
                        ),

                        // a_partition
                        array(
                            'new' => 'a_partition',
                            'old' => 'PortA',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    $portData = $this->resolvePortDN($objectDN, $portDN);
                                    if(isset($portData[$portName])) {
                                        return json_encode($portData[$portName]['partition_address']);
                                    } else {
                                        return 'Port not found';
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // a_port
                        array(
                            'new' => 'a_port',
                            'old' => 'PortA',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    $portData = $this->resolvePortDN($objectDN, $portDN);
                                    if(isset($portData[$portName])) {
                                        return $portData[$portName]['port_id'];
                                    } else {
                                        return 'Port not found';
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // a_cable_id
                        array(
                            'new' => 'a_cable_id',
                            'old' => 'CableA ID',
                            'process' => function($data) {

                                if($data == null or strtolower($data) == 'none') {
                                    return null;
                                } else {
                                    return $data;
                                }
                            }
                        ),

                        // b_id
                        array(
                            'new' => 'b_id',
                            'old' => 'PortB',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {
                                    return $this->conversionMap['object'][$objectDN];
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // b_face
                        array(
                            'new' => 'b_face',
                            'old' => 'PortB',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    $portData = $this->resolvePortDN($objectDN, $portDN);
                                    if(isset($portData[$portName])) {
                                        return $portData[$portName]['face'];
                                    } else {
                                        return 'Port not found';
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // b_partition
                        array(
                            'new' => 'b_partition',
                            'old' => 'PortB',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    $portData = $this->resolvePortDN($objectDN, $portDN);
                                    if(isset($portData[$portName])) {
                                        return json_encode($portData[$portName]['partition_address']);
                                    } else {
                                        return 'Port not found';
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // b_port
                        array(
                            'new' => 'b_port',
                            'old' => 'PortB',
                            'process' => function($portDN) {

                                if($portDN == null or strtolower($portDN) == 'none') {
                                    return null;
                                }
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    $portData = $this->resolvePortDN($objectDN, $portDN);
                                    if(isset($portData[$portName])) {
                                        return $portData[$portName]['port_id'];
                                    } else {
                                        return 'Port not found';
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // b_cable_id
                        array(
                            'new' => 'b_cable_id',
                            'old' => 'CableB ID',
                            'process' => function($data) {

                                if($data == null or strtolower($data) == 'none') {
                                    return null;
                                } else {
                                    return $data;
                                }
                            }
                        ),
                    )
                ),
                'trunk' => array(
                    '08 - Trunks.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => array('Trunk Peer A', 'Trunk Peer B'),
                            'process' => function($data) {
                                $conversionMapHash = implode(".", $data);
                                return $this->conversionMap['trunk'][$conversionMapHash];
                            }
                        ),

                        // a_id
                        array(
                            'new' => 'a_id',
                            'old' => 'Trunk Peer A',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {
                                    return $this->conversionMap['object'][$objectDN];
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // a_face
                        array(
                            'new' => 'a_face',
                            'old' => 'Trunk Peer A',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    // Get object floorplan type
                                    $object = $this->objectArray[$objectDN];
                                    $objectFloorplanType = $object['floorplan_object_type'];

                                    if($objectFloorplanType === null) {
                                        $portData = $this->resolvePortDN($objectDN, $portDN);
                                        if(isset($portData[$portName])) {
                                            return $portData[$portName]['face'];
                                        } else {
                                            return 'Port not found';
                                        }
                                    } else {
                                        return 'front';
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // a_partition
                        array(
                            'new' => 'a_partition',
                            'old' => 'Trunk Peer A',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    // Get object floorplan type
                                    $object = $this->objectArray[$objectDN];
                                    $objectFloorplanType = $object['floorplan_object_type'];

                                    if($objectFloorplanType === null) {
                                        $portData = $this->resolvePortDN($objectDN, $portDN);
                                        if(isset($portData[$portName])) {
                                            return json_encode($portData[$portName]['partition_address']);
                                        } else {
                                            return 'Port not found';
                                        }
                                    } else {
                                        return json_encode(array(0));
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // a_port
                        array(
                            'new' => 'a_port',
                            'old' => 'Trunk Peer A',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    // Get object floorplan type
                                    $object = $this->objectArray[$objectDN];
                                    $objectFloorplanType = $object['floorplan_object_type'];

                                    if($objectFloorplanType) {
                                        return 0;
                                    } else {
                                        return null;
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // b_id
                        array(
                            'new' => 'b_id',
                            'old' => 'Trunk Peer B',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {
                                    return $this->conversionMap['object'][$objectDN];
                                } else {
                                    return null;
                                }
                            }
                        ),

                        // b_face
                        array(
                            'new' => 'b_face',
                            'old' => 'Trunk Peer B',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    // Get object floorplan type
                                    $object = $this->objectArray[$objectDN];
                                    $objectFloorplanType = $object['floorplan_object_type'];

                                    if($objectFloorplanType === null) {
                                        $portData = $this->resolvePortDN($objectDN, $portDN);
                                        if(isset($portData[$portName])) {
                                            return $portData[$portName]['face'];
                                        } else {
                                            return 'Port not found';
                                        }
                                    } else {
                                        return 'front';
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // b_partition
                        array(
                            'new' => 'b_partition',
                            'old' => 'Trunk Peer B',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    // Get object floorplan type
                                    $object = $this->objectArray[$objectDN];
                                    $objectFloorplanType = $object['floorplan_object_type'];

                                    if($objectFloorplanType === null) {
                                        $portData = $this->resolvePortDN($objectDN, $portDN);
                                        if(isset($portData[$portName])) {
                                            return json_encode($portData[$portName]['partition_address']);
                                        } else {
                                            return 'Port not found';
                                        }
                                    } else {
                                        return json_encode(array(0));
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // b_port
                        array(
                            'new' => 'b_port',
                            'old' => array('Trunk Peer A', 'Trunk Peer B'),
                            'process' => function($data) {

                                $peerA = $data[0];
                                $peerB = $data[1];

                                $peerATrunkDNArray = explode(' ', $peerA);
                                $peerAportDN = $peerATrunkDNArray[0];
                                $peerAObjectDN = $this->extractObjectDN($peerAportDN);
                                $peerAPortName = $this->extractPortName($peerAObjectDN, $peerAportDN);

                                $peerBTrunkDNArray = explode(' ', $peerB);
                                $peerBportDN = $peerBTrunkDNArray[0];
                                $peerBObjectDN = $this->extractObjectDN($peerBportDN);
                                $peerBPortName = $this->extractPortName($peerBObjectDN, $peerBportDN);

                                if(isset($this->conversionMap['object'][$peerBObjectDN])) {

                                    // Get object floorplan type
                                    $peerAObject = $this->objectArray[$peerAObjectDN];
                                    $peerAObjectFloorplanType = $peerAObject['floorplan_object_type'];

                                    if($peerAObjectFloorplanType) {
                                        $portData = $this->resolvePortDN($peerBObjectDN, $peerBportDN);
                                        if(isset($portData[$peerBPortName])) {
                                            return $portData[$peerBPortName]['port_id'];
                                        } else {
                                            return 'Port not found';
                                        }
                                    } else {
                                        return null;
                                    }
                                } else {
                                    return 'Object not found';
                                }
                            }
                        ),

                        // group_id
                        array(
                            'new' => 'group_id',
                            'old' => 'Trunk Peer A',
                            'process' => function($trunkDN) {

                                $trunkDNArray = explode(' ', $trunkDN);
                                $portDN = $trunkDNArray[0];
                                
                                $objectDN = $this->extractObjectDN($portDN);
                                $portName = $this->extractPortName($objectDN, $portDN);

                                if(isset($this->conversionMap['object'][$objectDN])) {

                                    // Get object floorplan type
                                    $object = $this->objectArray[$objectDN];
                                    $objectFloorplanType = $object['floorplan_object_type'];

                                    if($objectFloorplanType) {
                                        if(isset($this->trunkGroupIDArray[$objectDN])) {
                                            return $this->trunkGroupIDArray[$objectDN];
                                        } else {
                                            $groupID = rand(1, 999999999);
                                            $this->trunkGroupIDArray[$objectDN] = $groupID;
                                            return $groupID;
                                        }
                                    } else {
                                        $groupID = rand(1, 999999999);
                                        return $groupID;
                                    }
                                }
                            }
                        )
                    )
                ),
                'port' => array(
                    '07 - Connections.csv' => array(

                        // id
                        array(
                            'new' => 'id',
                            'old' => array('PortA'),
                            'process' => function($data=null) {
                                $this->conversionEntryPasses = false;
                                return null;
                            }
                        ),

                        // object_id
                        array(
                            'new' => 'object_id',
                            'old' => array('PortA'),
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // object_face
                        array(
                            'new' => 'object_face',
                            'old' => array('PortA'),
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // object_partition
                        array(
                            'new' => 'object_partition',
                            'old' => array('PortA'),
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // port_id
                        array(
                            'new' => 'port_id',
                            'old' => array('PortA'),
                            'process' => function($data=null) {
                                return null;
                            }
                        ),

                        // description
                        array(
                            'new' => 'description',
                            'old' => array('PortA'),
                            'process' => function($data=null) {
                                return null;
                            }
                        ),
                    )
                )
            );

            $tableDependencies = array(
                'location' => array('parent_id', 'id'),
                'object' => array('parent_id', 'id')
            );

            // Create/update entries
            foreach($archiveSchemas as $tableName => $archiveSchema) {

                // Working CSV array
                $csvArray = array();

                $archiveConversionCounter = 1;
                foreach($archiveSchema as $archiveFilename => $attrConversions) {

                    // Open legacy archive file
                    $archiveFilePath = Storage::disk('local')->path('imports/'.$archiveFilename);
                    $file = fopen($archiveFilePath, 'r');
                    if($file !== FALSE) {

                        // populate conversion map
                        $row = 1;
                        while (($data = fgetcsv($file, 100000, ",")) !== FALSE) {

                            if($row == 1){
                                // Process header
                                $attrMap = $this->processCSVHeader($data);
                            }

                            $fieldData = array();
                            foreach($conversionMapping[$tableName][$archiveFilename]['fields'] as $field) {
                                array_push($fieldData, $data[$attrMap[$field]]);
                            }
                            $conversionMapping[$tableName][$archiveFilename]['process']($fieldData);

                            $row++;
                        }
                        
                        rewind($file);

                        // process rows
                        $row = 1;
                        while (($data = fgetcsv($file, 100000, ",")) !== FALSE) {

                            $this->conversionEntryPasses = true;
                            $this->conversionArchiveAddress = $archiveFilename.':'.$row;

                            if($row == 1){

                                // Process header
                                $attrMap = $this->processCSVHeader($data);
                                $workingArray = array();
                                
                                foreach($attrConversions as $attrIdx => $attrConversion) {
                                    array_push($workingArray, $attrConversion['new']);
                                }

                                // Add header to CSV file
                                if($archiveConversionCounter == 1) {
                                    array_push($csvArray, $workingArray);
                                }
                            } else {

                                // Process entry
                                $workingArray = array();
                                foreach($attrConversions as $attrIdx => $attrConversion) {

                                    // Initialize legacy data
                                    $legacyData = null;

                                    // Retrieve legacy data
                                    if($attrConversion['old']) {
                                        if(is_array($attrConversion['old'])) {
                                            $legacyData = array();
                                            foreach($attrConversion['old'] as $attr) {
                                                array_push($legacyData, $data[$attrMap[$attr]]);
                                            }
                                        } else {
                                            $legacyData = $data[$attrMap[$attrConversion['old']]];
                                        }
                                    }

                                    // Process legacy data
                                    $processedData = $attrConversion['process']($legacyData);

                                    // Add processed data to row
                                    array_push($workingArray, $processedData);
                                }

                                // Add row to CSV file
                                if($this->conversionEntryPasses) {
                                    array_push($csvArray, $workingArray);
                                }
                            }
                            $row++;
                        }

                    } else {
                        throw ValidationException::withMessages(['error' => 'Not able to open extracted file.']);
                    }

                    $archiveConversionCounter++;
                    fclose($file);
                }

                // Sort $csvArray according to dependencies
                if(isset($tableDependencies[$tableName])) {

                    // Initialize some variables
                    $parentIDAttr = $tableDependencies[$tableName][0];
                    $IDAttr = $tableDependencies[$tableName][1];
                    $workingArray = array();
                    $parentIDValueArray = array();

                    $row = 1;
                    foreach($csvArray as $csvRow) {

                        if($row == 1){

                            // Process header
                            $attrMap = $this->processCSVHeader($csvRow);
                            array_push($workingArray, $csvRow);
                        } else {
                        
                            // Collect dependecy data
                            $parentIDValue = $csvRow[$attrMap[$parentIDAttr]];
                            $IDValue = $csvRow[$attrMap[$IDAttr]];
                            
                            // Map parent ID to child ID
                            if($parentIDValue) {
                                $parentIDValueArray[$parentIDValue] = $IDValue;
                            }
                            
                            $inserted = false;
                            if(isset($parentIDValueArray[$IDValue])) {

                                // Get array index of child
                                $idx = $this->findIndex($parentIDValueArray[$IDValue], $workingArray, $attrMap[$IDAttr]);

                                if($idx !== false) {

                                    // Cut the working array in half where the row should be inserted
                                    $beginning = array_slice($workingArray, 0, $idx);
                                    $ending = array_slice($workingArray, $idx);

                                    // Recompile the working array inserting the row
                                    $workingArray = array();
                                    foreach($beginning as $elem) {
                                        array_push($workingArray, $elem);
                                    }
                                    array_push($workingArray, $csvRow);
                                    foreach($ending as $elem) {
                                        array_push($workingArray, $elem);
                                    }

                                    // Flag this row as being inserted
                                    $inserted = true;
                                }
                            }
                            
                            // Add row to CSV data
                            if(!$inserted) {
                                array_push($workingArray, $csvRow);
                            }
                        }
                        $row++;
                    }

                    $csvArray = $workingArray;
                }

                // Open temp stream to write CSV
                $csvFile = fopen('php://temp', 'w');
                foreach($csvArray as $csvRow) {
                    fputcsv($csvFile, $csvRow);
                }

                // Store CSV to disk
                $filename = $tableName.'.csv';
                Storage::disk('local')->put($filename, $csvFile);
                fclose($csvFile);

                // Add CSV to ZIP
                $zip->addFile(Storage::disk('local')->path($filename), $filename);
            }

            // Add image files to ZIP
            foreach($imgFileArray as $imgFileData) {
                $srcPath = Storage::disk('local')->path('imports/'.$imgFileData['path']);
                $dstPath = 'images/'.$imgFileData['name'];
                $zip->addFile($srcPath, $dstPath);
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
        $partitionArray = array();
        foreach($data as $partition) {

            $workingArray = array();
            $partitionType = strtolower($partition['partitionType']);
            $partitionDirection = $partition['direction'];
            
            if($depth == 0 && $partitionDirection == 'row') {

                // First partition is "row" so add generic 1st partition and proceed
                $workingArray['type'] = 'generic';
                $workingArray['prepended'] = true;
                $workingArray['units'] = intval($partition['hUnits']);
                $workingArray['children'] = array();
                $depth++;
                $workingArray['children'] = $this->processPartition($data, $depth);

            } else {

                $workingArray['type'] = $partitionType;
                $workingArray['units'] = ($partitionDirection == 'row') ? intval($partition['vUnits']) : intval($partition['hUnits']);
                $workingArray['children'] = array();

                switch($partitionType) {
                    case 'generic':
                        $depth++;
                        if(isset($partition['children'])) {
                            $workingArray['children'] = $this->processPartition($partition['children'], $depth);
                        }
                        break;

                    case 'connectable':
                        $workingArray['port_format'] = $this->processPortFormat($partition['portNameFormat']);
                        $workingArray['port_layout'] = array(
                            'cols' => intval($partition['valueX']),
                            'rows' => intval($partition['valueY'])
                        );
                        $workingArray['media'] = intval($partition['mediaType']);
                        $workingArray['port_connector'] = intval($partition['portType']);
                        $workingArray['port_orientation'] = intval($partition['portOrientation']);
                        break;

                    case 'enclosure':
                        $workingArray['enc_layout'] = array(
                            'cols' => intval($partition['valueX']),
                            'rows' => intval($partition['valueY'])
                        );
                        break;
                }
            }
            array_push($partitionArray, $workingArray);
        }

        return $partitionArray;
    }

    /**
     * get partition address
     *
     * @param   array $blueprint
     * @param   integer $partitionDepth
     * @return  array
     */
    private function getPartitionAddress($blueprint, $partitionDepth, $partitionAddress=array(0), &$depthCounter=0, $partitionIdx=0)
    {

        foreach($blueprint as $partition) {

            $partitionAddress[count($partitionAddress)-1] = $partitionIdx;

            if($depthCounter == $partitionDepth) {
                return $partitionAddress;
            }

            $depthCounter++;
            $partitionIdx++;

            if(count($partition['children'])) {
                $tempPartitionAddress = $partitionAddress;
                array_push($tempPartitionAddress, 0);
                
                $workingPartitionAddress = $this->getPartitionAddress($partition['children'], $partitionDepth, $tempPartitionAddress, $depthCounter);
                if($workingPartitionAddress) {
                    return $workingPartitionAddress;
                }
            }
            
        }
        return false;
    }

    /**
     * resolve port DN
     *
     * @param   string $portDN
     * @return  array
     */
    private function resolvePortDN($objectDN, $portDN)
    {
        $faceArray = array('front', 'rear');
        $portIDArray = array();

        if(isset($this->objectArray[$objectDN])) {

            // Retrieve template
            $templateName = $this->objectArray[$objectDN]['template_name'];
            $template = $this->templateArray[$templateName];

            // Store blueprint
            $blueprint = $template['blueprint'];

            foreach($faceArray as $face) {
                
                $this->findPortDN($blueprint[$face], $face, $portIDArray);
            }
        }

        return $portIDArray;
    }

    /**
     * find port DN
     *
     * @param   array   $partitionSet
     * @param   string  $portName
     * @return  array
     */
    private function findPortDN($partitionSet, $face, &$portIDArray, $partitionAddress=array(0))
    {

        // Loop through partition sets
        foreach($partitionSet as $partition) {

            // Set partition index
            $partitionSetIdx = count($partitionAddress)-1;

            // Search for matching port name
            if($partition['type'] == 'connectable') {

                // Gather some data
                $portLayout = $partition['port_layout'];
                $portFormat = $partition['port_format'];
                $portTotal = $portLayout['cols'] * $portLayout['rows'];

                for($x=0; $x<$portTotal; $x++) {
                    $portID = $this->generatePortID($x, $portTotal, $portFormat);
                    $portIDArray[$portID] = array(
                        'face' => $face,
                        'partition_address' => $partitionAddress,
                        'port_id' => $x,
                    );
                }
            }

            // Loop through children
            if(count($partition['children'])) {

                // Append new partition set index to temporary partition address
                $tempPartitionAddress = $partitionAddress;
                array_push($tempPartitionAddress, 0);

                // Process child partition set
                $this->findPortDN($partition['children'], $face, $portIDArray, $tempPartitionAddress);
            }

            // Increment partition set index
            $partitionAddress[$partitionSetIdx] = $partitionAddress[$partitionSetIdx] + 1;
            
        }
        return;
    }

    /**
     * generate port ID
     *
     * @param   int     $index
     * @param   int     $portTotal
     * @param   array   $portFormat
     * @return  array
     */
    function generatePortID($Index, $PortTotal, $PortFormat)
    {
        $portString = '';
		$incrementalCount = 0;
		
		// Create character arrays
		$lowercaseIncrementArray = array();
		$uppercaseIncrementArray = array();
		for($x=97; $x<=122; $x++) {
			array_push($lowercaseIncrementArray, chr($x));
		}
		for($x=65; $x<=90; $x++) {
			array_push($uppercaseIncrementArray, chr($x));
		}
		
		// Account for infinite count incrementals
		foreach($PortFormat as &$itemA) {
			$type = $itemA['type'];
			
			if($type == 'incremental' or $type == 'series') {
				$incrementalCount++;
				if($itemA['count'] == 0) {
					$itemA['count'] = $PortTotal;
				}
			}
		}
		
		foreach($PortFormat as $itemB) {
			$type = $itemB['type'];
			$value = $itemB['value'];
			$order = $itemB['order'];
			$count = $itemB['count'];
			
			if($type == 'static') {
				$portString = $portString.$value;
			} else if($type == 'incremental' or $type == 'series') {
				$numerator = 1;
				if($order < $incrementalCount) {
					foreach($PortFormat as $itemC) {
						$typeC = $itemC['type'];
						$orderC = $itemC['order'];
						$countC = $itemC['count'];
						
						if($typeC == 'incremental' or $typeC == 'series') {
							if($order < $orderC) {
								$numerator *= $countC;
							}
						}
					}
				}
				
				$howMuchToIncrement = floor($Index / $numerator);
				
				if($howMuchToIncrement >= $count) {
					$rollOver = floor($howMuchToIncrement / $count);
					$howMuchToIncrement = $howMuchToIncrement - ($rollOver * $count);
				}
				
				if($type == 'incremental') {
					if(is_numeric($value)) {
						$value = $value + $howMuchToIncrement;
						$portString = $portString.$value;
					} else {
						$asciiValue = ord($value);
						$asciiIndex = $asciiValue + $howMuchToIncrement;
						if($asciiValue >= 65 && $asciiValue <= 90) {
							// Uppercase
							
							while($asciiIndex > 90) {
								$portString = $portString.$uppercaseIncrementArray[0];
								$asciiIndex -= 26;
							}
							$portString = $portString.$uppercaseIncrementArray[$asciiIndex-65];
						} else if($asciiValue >= 97 && $asciiValue <= 122) {
							// Lowercase
							while($asciiIndex > 122) {
								$portString = $portString.$lowercaseIncrementArray[0];
								$asciiIndex -= 26;
							}
							$portString = $portString.$lowercaseIncrementArray[$asciiIndex-97];
						}
					}
					
				} else if($type == 'series') {
                    $value = explode(',', $value);
					$portString = $portString.$value[$howMuchToIncrement];
				}
			}
		}
			
		return $portString;
    }

    /**
     * extract object DN from port DN
     *
     * @param   string     $portDN
     * @return  string
     */
    function extractObjectDN($portDN)
    {
        $longestMatch = false;
        $maxLength = 0;
        
        foreach (array_keys($this->properToActualMapping) as $objectDNProper) {
            
            $length = strlen($objectDNProper);
            if (strpos($portDN, $objectDNProper) === 0 && $length > $maxLength) {
                $longestMatch = $objectDNProper;
                $maxLength = $length;
            }
        }

        if(!$longestMatch) {
            $portDNArray = explode('.', $portDN);
            array_pop($portDNArray);
            $objectDN = implode('.', $portDNArray);
        } else {
            $objectDN = $this->properToActualMapping[$longestMatch];
        }
        return $objectDN;
    }

    /**
     * extract object DN from port DN
     *
     * @param   string     $portDN
     * @return  string
     */
    function extractPortName($objectDN, $portDN)
    {
        $objectDNMerged = str_replace('.', '', $objectDN);
        $portDNMerged = str_replace('.', '', $portDN);

        $portName = str_replace($objectDNMerged, '', $portDNMerged);

        return $portName;
    }

    /**
     * generate random alphanumeric string of 40 characters
     *
     * @return  string
     */
    function generateFilename()
    {
        $length = 40;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $maxCharIndex = strlen($characters) - 1;
    
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $maxCharIndex)];
        }
    
        return $randomString;
    }

    /**
     * process port_format
     *
     * @return  string
     */
    function processPortFormat($portFormat)
    {

        $portFormatArray = array();
        foreach($portFormat as $portFormatField) {

            $addPortFormatField = true;

            // Exclude static fields with empty value
            if($portFormatField['type'] == "static" && $portFormatField['value'] == "") {
                $addPortFormatField = false;
            }

            // Convert series array into comma delimited string
            if($portFormatField['type'] == 'series') {
                $portFormatField['value'] = implode(',', $portFormatField['value']);
            }

            $portFormatField['type'] = strval($portFormatField['type']);
            $portFormatField['value'] = strval($portFormatField['value']);
            $portFormatField['count'] = intval($portFormatField['count']);
            $portFormatField['order'] = intval($portFormatField['order']);

            if($addPortFormatField) {
                array_push($portFormatArray, $portFormatField);
            }
        }
        return $portFormatArray;
    }

    /**
     * process port_format
     *
     * @param   integer $needle
     * @param   array   $haystack
     * @param   integer $IDIndex
     * @return  string
     */
    function findIndex($needle, $haystack, $IDIndex)
    {
        foreach($haystack as $idx => $hay) {
            if($hay[$IDIndex] == $needle) {
                return $idx;
            }
        }
        return false;
    }
}
