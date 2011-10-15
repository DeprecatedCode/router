<?php

namespace Evolution\Router;
use Evolution\Kernel\Service;

/**
 * Add the routing service
 */
Service::bind('Evolution\Router\Bundle::route', 'kernel:startup');