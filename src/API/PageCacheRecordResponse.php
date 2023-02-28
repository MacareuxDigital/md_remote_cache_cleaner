<?php

namespace Macareux\RemoteCacheCleaner\API;

use Concrete\Core\Cache\Page\PageCacheRecord;

class PageCacheRecordResponse
{
    protected $cached = false;

    protected $expiration = 0;

    public function __construct(?PageCacheRecord $pageCacheRecord = null)
    {
        if ($pageCacheRecord) {
            $this->expiration = $pageCacheRecord->getCacheRecordExpiration();
            $this->cached = true;
        }
    }

    public function getJsonObject(): \stdClass
    {
        $obj = new \stdClass();
        $obj->cached = $this->cached;
        $obj->expiration = $this->expiration;

        return $obj;
    }
}
