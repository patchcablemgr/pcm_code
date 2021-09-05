<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\TemplateModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

class TemplateInsertParentData implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        
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
			
				$PCM = new PCM;
				$parentTemplateID = $value['id'];
				$parentTemplateFace = $value['face'];
				$parentTemplatePartitionAddress = $value['partition_address'];
				
        // Confirm template exists
				$parentTemplate = TemplateModel::where('id', '=', $value['id'])->first();
				if ($parentTemplate === null) {
					return false;
				}
				
				// Confirm template face is valid
				if($parentTemplateFace == 'rear' and $parentTemplate['mount_config'] == '2-post') {
					return false;
				}
				
				// Retrieve partition
				$partition = $PCM->getPartition($parentTemplate['blueprint'], $parentTemplateFace, $parentTemplatePartitionAddress);
				if(!$partition) {
					return false;
				}
				
				// Confirm template partition exists
				if($partition) {
					
					// Confirm template partition is enclosure
					if($partition['type'] != 'enclosure') {
						return false;
					}
				} else {
					return false;
				}
				
				return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid insert parent data.';
    }
}
