<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/md_remote_cache_cleaner
 * Namespace: Macareux\RemoteCacheCleaner\API\Controller
 */
$router->get('/check_cache', 'Cache::check');
$router->post('/clear_cache', 'Cache::clear');
