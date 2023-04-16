<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Base64Img implements Rule
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

        // Remove data URI scheme prefix if present
        $value = preg_replace('#^data:image/\w+;base64,#i', '', $value);

        // Decode base64 string
        $decodedImage = base64_decode($value, true);

        // Check if decoding was successful
        if ($decodedImage === false) {
            return false; // Invalid base64 string
        }

        // Check if the decoded data is a valid image
        $imageInfo = getimagesizefromstring($decodedImage);
        if ($imageInfo === false) {
            return false; // Not a valid image
        }

        // Check if the image type is supported (you can add or remove supported image types as needed)
        $supportedImageTypes = [
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_GIF
        ];
        if (!in_array($imageInfo[2], $supportedImageTypes)) {
            return false; // Unsupported image type
        }

        return true; // Valid base64 encoded image
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid template image. '.$this->archiveAddress;
    }
}
