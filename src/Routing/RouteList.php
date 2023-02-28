<?php

namespace Macareux\RemoteCacheCleaner\Routing;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router->buildGroup()
            ->setPrefix('/ccm/md_remote_cache_cleaner')
            ->setNamespace('Macareux\RemoteCacheCleaner\API\Controller')
            ->routes('api.php', 'md_remote_cache_cleaner')
        ;
    }
}
