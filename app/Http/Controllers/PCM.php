<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PCM extends Controller
{
    /**
     * Return partition 
     *
     * @param  obj  $blueprint
	 * @param  str  $face
	 * @param  arr  $partitionAddress
     * @return \Illuminate\View\View
     */
    public function getPartition($blueprint, $face, $partitionAddress)
    {
        // Locate template partition
		$partition = false;
		$partitionCollection = $blueprint[$face];
		
		foreach($partitionAddress as $partitionIndex) {
			
			if(isset($partitionCollection[$partitionIndex])) {
				
				$partition = $partitionCollection[$partitionIndex];
				$partitionCollection = $partitionCollection[$partitionIndex]['children'];
				
			} else {
				return false;
			}
		}
		
		return $partition;
    }

	/**
     * Return patched blueprint
     *
     * @param  obj  $blueprint
	 * @param  str  $face
	 * @param  arr  $partitionAddress
	 * @param  obj  $partitionPatched
     * @return \Illuminate\View\View
     */
    public function patchPartition($blueprint, $face, $partitionAddress, $partitionPatched)
    {
        // Initialize partition collection for loop
		$workingBlueprint = $blueprint;
		$partitionCollection = &$workingBlueprint[$face];
		
		// Loop over partition address until selected partition has been reached
		foreach($partitionAddress as $index => $partitionIndex) {
			
			// Test if partition address is valid
			if(isset($partitionCollection[$partitionIndex])) {
				
				// Test if selected partition has been reached
				if($index == (count($partitionAddress) - 1)) {

					// Patch partition
					$partitionCollection[$partitionIndex] = $partitionPatched;
				}
				$partitionCollection = &$partitionCollection[$partitionIndex]['children'];
				
			} else {
				return false;
			}
		}
		
		// Return entire blueprint with patched partition
		return $workingBlueprint;
    }
}