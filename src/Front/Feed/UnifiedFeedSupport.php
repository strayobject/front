<?php
declare(strict_types=1);

namespace Front\Feed;

use Front\Feed\UnifiedFeed;

trait UnifiedFeedSupport
{
    private $unifiedFeed;

    protected function getUnifiedFeed()
    {
        if (!is_null($this->unifiedFeed)) {
            return $this->unifiedFeed();
        } else {

        }

    }
}
