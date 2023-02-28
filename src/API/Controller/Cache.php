<?php

namespace Macareux\RemoteCacheCleaner\API\Controller;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Cache\Page\PageCache;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Macareux\RemoteCacheCleaner\API\PageCacheRecordResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class Cache implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var ErrorList
     */
    protected $error;

    /**
     * @param ErrorList $error
     */
    public function __construct(ErrorList $error)
    {
        $this->error = $error;
    }

    public function check()
    {
        $page = $this->validateRequest();

        if (!$this->error->has()) {
            $library = PageCache::getLibrary();
            $record = $library->getRecord($page);
            $response = new PageCacheRecordResponse($record);

            return new JsonResponse($response->getJsonObject());
        }

        return $this->error->createResponse();
    }

    public function clear()
    {
        $page = $this->validateRequest();

        if (!$this->error->has()) {
            $library = PageCache::getLibrary();
            $library->purge($page);
            $record = $library->getRecord($page);
            $response = new PageCacheRecordResponse($record);

            return new JsonResponse($response->getJsonObject());
        }

        return $this->error->createResponse();
    }

    protected function validateRequest(): ?Page
    {
        $request = Request::getInstance();
        $path = $request->get('path');
        $key = $request->get('key');

        $config = $this->getConfig();
        if ($key !== $config->get('server.key')) {
            $this->error->add(t('Invalid Key.'));
        }

        if ($path) {
            $page = Page::getByPath($path);
            if (!$page || $page->isError()) {
                $this->error->add(t('Invalid Path.'));
            }
        } else {
            $this->error->add(t('Page Path is required.'));
            $page = null;
        }

        return $page;
    }

    protected function getConfig(): ?\Concrete\Core\Config\Repository\Liaison
    {
        /** @var PackageService $service */
        $service = $this->app->make(PackageService::class);
        $class = $service->getClass('md_remote_cache_cleaner');

        return $class->getDatabaseConfig();
    }
}
