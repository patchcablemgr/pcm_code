<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ObjectModel;
use App\Models\TemplateModel;

class ConnectionPeerData implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $valid = true;

        foreach($value as $peer) {

            $peerKeyArray = array(
                'id',
                'face',
                'partition',
                'port_id',
            );

            // Validate peer data array
            foreach($peerKeyArray as $peerKey) {

                if(!array_key_exists($peerKey, $peer)) {

                    $valid = false;
                }
            }

            // Validate peer data
            if($valid) {

                // Skip peer data validation if port is populated and not connected
                if($peer['id'] !== null && $peer['face'] !== null && $peer['partition'] !== null && $peer['port_id'] !== null) {

                    // Validate peer object ID
                    $peerObject = ObjectModel::where('id', '=', $peer['id'])->first();
                    if ($peerObject === null) {

                        $valid = false;
                    } else {

                        // Validate peer face
                        $peerTemplateID = $peerObject->template_id;
                        $peerTemplate = TemplateModel::where('id', '=', $peerTemplateID)->first();
                        $faceArray = ($peerTemplate->mount_config == '2-post') ? ['front'] : ['front','rear'];
                        if(!in_array($peer['face'], $faceArray)) {
                            $valid = false;
                        }

                        // Validate peer partition
                        foreach($peer['partition'] as $partitionAddress) {
                            if(!is_int($partitionAddress)) {
                                $valid = false;
                            }
                        }

                        if(!is_int($peer['port_id'])) {
                            $valid = false;
                        }
                    }
                }
            }
        }
        
        return $valid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid peer data.';
    }
}
