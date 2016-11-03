<?php
declare(strict_types=1);

namespace Front\Feed;

use Amp\{CombinatorException, function all};

class UnifiedFeed
{
    public function __construct(Callable ...$feedProviders)
    {
        $this->feedProviders = $feedProviders;
    }
    /**
     * Merge all the feeds and sort by key
     * @return Generator
     */
    public function __invoke(): \Generator
    {
        try {
            /**
             * @todo we could use some() here, but it should probably be an option
             */
            $feed = (yield all($this->initProviders()));
            $merged = array_merge(...$feed);
            krsort($merged);

            return $merged;
        } catch (CombinatorException $e) {
            /**
             * @todo add logging
             */
            echo $e->getMessage(), "\n";
        }
    }

    /**
     * We would have had this in the constructor, however since object of this class
     * is created in the bootstrapper we need this functionality to be triggered
     * with every request.
     *
     * @return array
     */
    private function initProviders(): array
    {
        return array_map(function($provider) {
            return $provider->__invoke();
        }, $this->feedProviders);
    }
}
