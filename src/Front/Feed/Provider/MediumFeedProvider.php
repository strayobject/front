<?php
declare(strict_types=1);

namespace Front\Feed\Provider;

use Amp\{Deferred, Promise, function immediately};
use PicoFeed\{PicoFeedException, Reader\Reader};
use Front\Feed\FeedItem;

/**
 * @todo add cache
 */
class MediumFeedProvider implements Promise
{
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var array
     */
    private $source = [];
    /**
     * @var array
     */
    private $feed = [];
    /**
     * @var string
     */
    private $error = '';
    /**
     * @var array
     */
    private $whens = [];
    /**
     * @var array
     */
    private $watchers = [];

    public function __construct(Reader $reader, string $url)
    {
        $this->reader = $reader;
        $this->url = $url;
    }
    /**
     * Wrapper for the main functionality.
     * @return Promise
     */
    public function __invoke(): Promise
    {
        $deferred = new Deferred;

        immediately(function() use ($deferred) {
            $deferred->succeed($this->do());
        });

        return $deferred->promise();
    }
    /**
     * @return array
     */
    public function do(): array
    {
        try {
            $this->fetch();
            $this->parse();
        } catch (PicoFeedException $e) {
            $this->error = 'we failed';
        }

        return $this->feed;
    }
    /**
     * Fetch data from remote source, populate $source with the result
     * @todo error handling
     * @return void
     */
    private function fetch(): void
    {
        $resource = $this->reader->download($this->url);
        $parser = $this->reader->getParser(
            $resource->getUrl(),
            $resource->getContent(),
            $resource->getEncoding()
        );
        $feed = $parser->execute();
        $this->source = $feed;
    }
    /**
     * Parse source data into the $feed
     * @todo error handling
     * @return void
     */
    private function parse(): void
    {
        if (empty($this->source)) {
            throw new \Exception('Empty source, nothing to do.');
        }

        foreach($this->source->items as $item) {
            if ($item->getDate() instanceof \DateTime) {
                $date = $item->getDate()->format('U');
            } else {
                $date = (string) $item->getDate();
            }
            $feedItem = new FeedItem();
            $feedItem->setDate(new \DateTime('@'.$date));
            $feedItem->setTitle($item->getTitle());
            $feedItem->setContent($item->getContent());
            $feedItem->setPlatform('medium');
            $feedItem->setPlatformImage('https://developers.medium.com/img/icons/engineering.png');
            $feedItem->setPlatformLink($item->getUrl());
            $this->feed[$date.'-medium'] = $feedItem;
        }
    }

    public function when(callable $func, $data = null): MediumFeedProvider
    {
        if (!empty($this->feed)) {
            $func($this->error, $this->feed, $data);
        } else {
            $this->whens[] = [$func, $data];
        }
        return $this;
    }

    public function watch(callable $func, $data = null): MediumFeedProvider
    {
        if (!isset($this->feed)) {
            $this->watchers[] = [$func, $data];
        }
        return $this;
    }
}
