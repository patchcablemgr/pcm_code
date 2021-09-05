<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

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
}