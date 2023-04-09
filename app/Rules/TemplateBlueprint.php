<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\MediaModel;
use App\Models\TemplateModel;
use App\Models\PortConnectorModel;
use App\Models\PortOrientation;
use Illuminate\Support\Facades\Log;

class TemplateBlueprint implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($request, $id, $face, $archiveAddress=null)
    {

        $this->returnMessage = 'Unknown error.';
        $this->ignore = false;
        $this->archiveAddress = $archiveAddress;

        if(!is_null($id)) {
            
            // Update operation
            Log::info('Update template type: '.$template['type']);
            $template = TemplateModel::where('id', $id)->first();
            if($template['type'] == 'standard') {
                $width = 24;
                $height = $template['ru_size']*2;
            } else {
                if($face == 'rear') {
                    $this->ignore = true;
                }
                $width = $template['insert_constraints'][0]['part_layout']['width'];
                $height = $template['insert_constraints'][0]['part_layout']['height'];
            }
        } else {

            // Create operation
            Log::info('Create template type: '.$request->type);
            if($request->type == 'standard') {
                $width = 24;
                $height = $request->ru_size*2;
            } else {
                if($face == 'rear') {
                    $this->ignore = true;
                }
                $width = $request->insert_constraints[0]['part_layout']['width'];
                $height = $request->insert_constraints[0]['part_layout']['height'];
            }
        }

        $this->unitsAvailable = array(
            $width,
            $height
        );

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->ignore) {
            return true;
        } else {
            return $this->validatePartition($value, $this->unitsAvailable);
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->returnMessage;
    }

    /**
     * Validate template partitions
     *
     * @return string
     */
    private function validatePartition($partitionCollection, $unitsAvailable, $depth=0)
    {

        $valid = true;
        $partitionTypes = array(
            'generic',
            'connectable',
            'enclosure',
        );
        $partitionKeys = array(
            'type' => 'string',
            'units' => 'integer',
            'children' => 'array'
        );
        $connectablePartitionKeys = array(
            'port_format' => 'array',
            'port_layout' => 'array',
            'media' => 'integer',
            'port_connector' => 'integer',
            'port_orientation' => 'integer'
        );
        $enclosurePartitionKeys = array(
            'enc_layout' => 'array'
        );
        $layoutKeys = array(
            'cols' => 'integer',
            'rows' => 'integer'
        );

        foreach($partitionCollection as $partition) {
            
            // Validate partition keys
            if(count(array_intersect(array_keys($partition), array_keys($partitionKeys))) == count($partitionKeys)) {

                // Validate partition key types
                $validTypes = true;
                foreach($partitionKeys as $partitionKey => $partitionKeyType) {
                    $validTypes = (gettype($partition[$partitionKey]) == $partitionKeyType) ? $validTypes : false;
                }
                if($validTypes) {

                    // Validate partition units
                    $units = $partition['units'];
                    Log::info($units.' - '.$unitsAvailable[round($depth%2)]);
                    Log::info($unitsAvailable);
                    if($units <= $unitsAvailable[round($depth%2)]) {

                        // Validate partition type
                        if(in_array($partition['type'], $partitionTypes)) {

                            // Generic partition
                            if($partition['type'] == 'generic') {

                                // Validate depth
                                if($depth < 100) {

                                    // Recurse
                                    $unitsAvailable[round($depth%2)] = $units;
                                    return $this->validatePartition($partition['children'], $unitsAvailable, $depth+1);
                                } else {
                                    $this->setErrorMessage('Invalid blueprint depth.  Depth: '.$depth);
                                    $valid = false;
                                }
                            // Connectable partition
                            } elseif($partition['type'] == 'connectable') {

                                // Validate children array is empty
                                if(count($partition['children']) > 0) {
                                    $this->setErrorMessage('Connectable partition "children" array must be empty.  Depth: '.$depth);
                                    $valid = false;
                                }

                                // Validate connectable partition keys
                                if(count(array_intersect(array_keys($partition), array_keys($connectablePartitionKeys))) == count($connectablePartitionKeys)) {

                                    // Validate partition key types
                                    $validTypes = true;
                                    foreach($connectablePartitionKeys as $partitionKey => $partitionKeyType) {
                                        $validTypes = (gettype($partition[$partitionKey]) == $partitionKeyType) ? $validTypes : false;
                                    }
                                    if($validTypes) {

                                        // Validate port_format
                                        if(!$this->validatePortFormat($partition['port_format'])) {
                                            //$this->returnMessage = 'Invalid port format.  Depth: '.$depth;
                                            $valid = false;
                                        }

                                        // Validate port_layout keys
                                        if(count(array_intersect(array_keys($partition['port_layout']), array_keys($layoutKeys))) == count($layoutKeys)) {

                                            // Validate port_layout key types
                                            $validTypes = true;
                                            foreach($layoutKeys as $layoutKey => $layoutKeyType) {
                                                $validTypes = (gettype($partition['port_layout'][$layoutKey]) == $layoutKeyType) ? $validTypes : false;
                                            }
                                            if($validTypes) {
                                                if($partition['port_layout']['cols'] > 100 && $partition['port_layout']['rows'] > 100) {
                                                    $this->setErrorMessage('Invalid connectable port layout.  Max is 100.  Depth: '.$depth);
                                                    $valid = false;
                                                }
                                            } else {
                                                $this->setErrorMessage('Invalid connectable port layout key types.  Depth: '.$depth);
                                                $valid = false;
                                            }
                                        } else {
                                            $this->setErrorMessage('Invalid connectable port layout keys.  Depth: '.$depth);
                                            $valid = false;
                                        }

                                        // Validate media
                                        if (MediaModel::where('value', '=', $partition['media'])->count() == 0) {
                                            $this->setErrorMessage('Invalid connectable port media.  Depth: '.$depth);
                                            $valid = false;
                                        }

                                        // Validate port_connector
                                        if (PortConnectorModel::where('value', '=', $partition['port_connector'])->count() == 0) {
                                            $this->setErrorMessage('Invalid connectable port connector.  Depth: '.$depth);
                                            $valid = false;
                                        }

                                        // Validate port_orientation
                                        if (PortOrientation::where('value', '=', $partition['port_orientation'])->count() == 0) {
                                            $this->setErrorMessage('Invalid connectable port orientation.  Depth: '.$depth);
                                            $valid = false;
                                        }
                                    } else {
                                        $this->setErrorMessage('Invalid connectable partition key types.  Depth: '.$depth);
                                        $valid = false;
                                    }
                                } else {
                                    $this->setErrorMessage('Invalid connectable partition keys.  Depth: '.$depth);
                                    $valid = false;
                                }

                            // Enclosure partition
                            } elseif($partition['type'] == 'enclosure') {

                                // Validate children array is empty
                                if(count($partition['children']) > 0) {
                                    $this->setErrorMessage('Enclosure partition "children" array must be empty.  Depth: '.$depth);
                                    $valid = false;
                                }

                                // Validate enclosure partition keys
                                if(count(array_intersect(array_keys($partition), array_keys($enclosurePartitionKeys))) == count($enclosurePartitionKeys)) {

                                    // Validate enclosure partition key types
                                    $validTypes = true;
                                    foreach($enclosurePartitionKeys as $partitionKey => $partitionKeyType) {
                                        $validTypes = (gettype($partition[$partitionKey]) == $partitionKeyType) ? $validTypes : false;
                                    }
                                    if($validTypes) {

                                        // Validate enc_layout keys
                                        if(count(array_intersect(array_keys($partition['enc_layout']), array_keys($layoutKeys))) == count($layoutKeys)) {

                                            // Validate enc_layout key types
                                            $validTypes = true;
                                            foreach($layoutKeys as $layoutKey => $layoutKeyType) {
                                                $validTypes = (gettype($partition['enc_layout'][$layoutKey]) == $layoutKeyType) ? $validTypes : false;
                                            }
                                            if($validTypes) {
                                                if($partition['enc_layout']['cols'] > 100 && $partition['enc_layout']['rows'] > 100) {
                                                    $this->setErrorMessage('Invalid enclosure layout.  Max is 100.  Depth: '.$depth);
                                                    $valid = false;
                                                }
                                            } else {
                                                $this->setErrorMessage('Invalid enclosure layout key types.  Depth: '.$depth);
                                                $valid = false;
                                            }
                                        } else {
                                            $this->setErrorMessage('Invalid enclosure layout keys.  Depth: '.$depth);
                                            $valid = false;
                                        }
                                    } else {
                                        $this->setErrorMessage('Invalid enclosure partition key types.  Depth: '.$depth);
                                        $valid = false;
                                    }
                                } else {
                                    $this->setErrorMessage('Invalid connectable partition keys.  Depth: '.$depth);
                                    $valid = false;
                                }
                            } else {
                                $this->setErrorMessage('Invalid partition type.  Depth: '.$depth);
                                $valid = false;
                            }
                        } else {
                            $this->setErrorMessage('Invalid partition type.  Depth: '.$depth);
                            $valid = false;
                        }
                    } else {
                        $this->setErrorMessage('Invalid partition size.  Depth: '.$depth);
                        $valid = false;
                    }
                } else {
                    $this->setErrorMessage('Invalid partition key types.  Depth: '.$depth);
                    $valid = false;
                }
            } else {
                $this->setErrorMessage('Missing partition key.  Depth: '.$depth);
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Validate port format
     *
     * @return string
     */
    private function validatePortFormat($portFormat)
    {

        $fieldKeys = array(
            'type' => 'string',
            'value' => 'string',
            'count' => 'integer',
            'order' => 'integer'
        );
        $fieldTypes = array(
            'static',
            'incremental',
            'series'
        );

        foreach($portFormat as $field) {

            // Validate field keys
            if(count(array_intersect(array_keys($field), array_keys($fieldKeys))) == count($fieldKeys)) {

                // Validate field key types
                $validTypes = true;
                foreach($fieldKeys as $key => $keyType) {
                    $validTypes = (gettype($field[$key]) == $keyType) ? $validTypes : false;
                }
                if($validTypes) {

                    // Validate field type
                    if(in_array($field['type'], $fieldTypes)) {
                        
                        // Static field
                        if($field['type'] == 'static') {

                            // Validate value
                            $pattern = "/^[a-zA-Z0-9\\\\\/\-\_\=\+\|\*]+$/";
                            if(!preg_match($pattern, $field['value'])) {
                                $this->setErrorMessage('Invalid static field value.');
                                return false;
                            }

                        // Incremental field
                        } else if($field['type'] == 'incremental') {

                            // Validate value
                            if(!preg_match('/^([1-9][0-9]*|[a-z]|[A-Z])$/', $field['value'])) {
                                $this->setErrorMessage('Invalid incremental field value.');
                                return false;
                            }

                        // Series field
                        } else if($field['type'] == 'series') {

                            // Validate value
                            if(!preg_match('/^(,?[a-zA-Z0-9\\\\\/\-\_\=\+\|\*])*$/', $field['value'])) {
                                $this->setErrorMessage('Invalid incremental field value.');
                                return false;
                            }
                        }
                    } else {
                        $this->setErrorMessage('Invalid field type.');
                        return false;
                    }

                    
                } else {
                    $this->setErrorMessage('Invalid field key types.');
                    return false;
                }
            } else {
                $this->setErrorMessage('Invalid field keys.');
                return false;
            }
        }
        return true;
    }

    /**
     * Set error message
     *
     * @param string errMsg
     * @return string
     */
    private function setErrorMessage($errMsg)
    {
        $this->returnMessage = ($this->archiveAddress) ? $errMsg.' '.$this->archiveAddress : $errMsg;
        return true;
    }
}
