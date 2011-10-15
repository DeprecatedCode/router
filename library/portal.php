<?php

namespace Evolution\Router;
use Evolution\Kernel\Configure;
use Evolution\Kernel\Service;
use Evolution\Kernel\Completion;
use Evolution\Kernel\IncompleteException;
use Exception;

/**
 * Router Portal
 * @author Nate Ferrero
 */
class Portal {
	
	public static $currentPortal;

    // Route the portal
    public static function route($path) {
        
        // Check for null first segment
        if(!isset($path[0]))
            return false;
        
        // Paths where this portal exists
        $dirs = array();
        
        // Portal Name
        $name = strtolower($path[0]);
        
        // Get portal paths
        $searchdirs = Configure::getArray('portal.location');
        
        // Check for portal in paths
        foreach($searchdirs as $dir) {
            $dir .= '/' . $name;
            if(is_dir($dir))
                $dirs[] = $dir;
        }
        
        // If any paths matched
        if(count($dirs) > 0) {
            
            // Remove the first segment
            array_shift($path);
            
            // Process the portal bindings
            try {
            	self::$currentPortal = $dirs;
                Service::complete('portal:route', $path, $dirs);eval(d);
            }
            
            // Handle successful routing
            catch(Completion $c) {
                throw $c;
            }
            
            // Handle any exceptions
            catch(Exception $e) {
                
                // Try to resolve with error pages and prevent exceptions here
                try {
                	Service::complete('portal:exception', $dirs, $path, $e);
                } catch(IncompleteException $x) {
                	throw $e;
                }
                
                // Else throw the error
                throw $e;
            }
        }
    }
    
    // Show portal directories
    public static function portalDirs() {
    	$out = '<h4>Portal Locations</h4><div class="trace">';
    	foreach(Configure::getArray('portal.location') as $dir) {
    		// Get portals in dir
    		$list = glob("$dir/*", GLOB_ONLYDIR);
    		foreach($list as $index => $item) {
    			$list[$index] = basename($list[$index]);
    			if(in_array($item, self::$currentPortal))
    				$list[$index] = '<span class="class selected" title="This is the current portal">'.$list[$index].'</span>';
    			else
    				$list[$index] = '<span class="class">'.$list[$index].'</span>';
    		}
    		$portals = implode(' &bull; ', $list);
    		if($portals != '')
    			$portals = ": $portals";
    		$out .= '<div class="step"><span class="file">'.$dir.$portals.'</span></div>';
    	}
    	$out .= '</div>';
    	return $out;
    }
}

/**
 * Show portal directories in message info
 */
Service::bind('Evolution\\Router\\Portal::portalDirs', 'kernel:message:information');