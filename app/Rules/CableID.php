<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\CableModel;
use Illuminate\Support\Facades\Log;

class CableID implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($archiveAddress=null)
    {
        $this->archiveAddress = $archiveAddress;
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

        $cable = CableModel::where('a_id', '=', $value)
            ->orwhere('b_id', '=', $value)
            ->first();
        
        return ($cable) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid cable ID. '.$this->archiveAddress;
    }
}
