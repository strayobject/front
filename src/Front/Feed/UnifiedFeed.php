<?php
declare(strict_types=1);

namespace Front\Feed;

use Amp\{CombinatorException, Redis\Client, function all};

class UnifiedFeed
{
    private $cache;
    private $name;
    private $feedProviders;

    public function __construct(Client $cache, string $name, Callable ...$feedProviders)
    {
        $this->feedProviders = $feedProviders;
        $this->name = $name;
        $this->cache = $cache;
    }
    /**
     * Merge all the feeds and sort by key
     * @return Generator
     */
    public function __invoke(): \Generator
    {
        try {
            $cached = yield $this->cache->get($this->name);

            if (!empty($cached)) {
                return unserialize($cached);
            }

            /**
             * @todo we could use some() here, but it should probably be an option
             */
            $feed = (yield all($this->initProviders()));
            $merged = array_merge(...$feed);
            krsort($merged);
            $this->cache->set($this->name, serialize($merged), 900);

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
