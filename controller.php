<?php

namespace Concrete\Package\MdRemoteCacheCleaner;

use Concrete\Core\Package\Package;
use Concrete\Core\Routing\RouterInterface;
use Macareux\RemoteCacheCleaner\Install\Installer;
use Macareux\RemoteCacheCleaner\Routing\RouteList;

class Controller extends Package
{
    protected $appVersionRequired = '9.0.0';

    protected $pkgHandle = 'md_remote_cache_cleaner';

    protected $pkgVersion = '0.0.1';

    protected $pkgAutoloaderRegistries = [
        'src' => '\Macareux\RemoteCacheCleaner',
    ];

    public function getPackageName()
    {
        return t('Macareux Remote Cache Cleaner');
    }

    public function getPackageDescription()
    {
        return t('A Concrete CMS package to clear cache on another instance.');
    }

    public function install()
    {
        $pkg = parent::install();

        /** @var Installer $installer */
        $installer = $this->app->make(Installer::class, ['package' => $pkg]);
        $installer->install();

        return $pkg;
    }

    public function on_start()
    {
        /** @var RouterInterface $router */
        $router = $this->app->make(RouterInterface::class);
        $router->loadRouteList(new RouteList());
    }
}
