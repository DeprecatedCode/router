<?php

namespace Evolution\Router;
use Evolution\Kernel\Service;
use Evolution\Kernel\Completion;
use Evolution\Kernel\IncompleteException;
use Exception;

/**
 * Router Bundle
 */
class Bundle {
	
	public static function route() {
		
		// Get url
		$url = $_SERVER['REDIRECT_URL'];
		
		// Get path array and clean
        $path = explode('/', $url);
        if($path[0] === '')
            array_shift($path);
        if($path[count($path) - 1] === '')
            array_pop($path);
            
        // Get clean path url
        $url = '/' . implode('/', $path);
		
		try {

			// Execute service
			Service::complete('router:route', $path);
		}
		catch(Completion $c) {
			die;
		}
		catch(IncompleteException $c) {
			
			// No path matched
			throw new NotFoundException("No resource found at `$url`", 0, $c);
		}
		catch(Exception $e) {
			throw new RoutingException($e, $url);
		}
	}
}

/**
 * Used when a resource is not found
 */
class NotFoundException extends Exception {}

/**
 * Used when a resource is not found
 */
class RoutingException extends Exception {
	
	private $url;

	public function __construct($previous, $url) {
		$this->url = $url;
		parent::__construct("Exception on URL `$this->url`", $previous->getCode(), $previous);
	}
}

/**
 * Add standard services
 */
Service::bind('Evolution\Router\Portal::route', 	'router:route');
Service::bind('Evolution\Router\Controller::route', 'router:route', 'portal:route');