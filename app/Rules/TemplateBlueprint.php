<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TemplateBlueprint implements Rule
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

        if(!array_key_exists('front', $value) || !array_key_exists('rear', $value)) {
            $valid = false;
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
        return 'Invalid template blueprint.';
    }
}
