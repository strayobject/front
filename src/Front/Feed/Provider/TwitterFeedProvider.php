<?php
declare(strict_types=1);

namespace Front\Feed\Provider;

use Abraham\TwitterOAuth\TwitterOAuth;
use Amp\{Deferred, Promise, function immediately};
use Front\Feed\FeedItem;

class TwitterFeedProvider implements Promise
{
    /**
     * @var TwitterOAuth
     */
    private $twitter;
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

    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
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
        $this->fetch();
        $this->parse();

        return $this->feed;
    }
    /**
     * Fetch data from remote source, populate $source with the result
     * @todo error handling
     * @todo move setup data out
     * @return void
     */
    private function fetch(): void
    {
        $feed = $this
            ->twitter
            ->get(
                'statuses/user_timeline',
                [
                    'count' => '50',
                    'user_id' => 'strayobject',
                    'screen_name' => 'strayobject',
                    'exclude_replies' => true,
                    'trim_user' => true,
                    'include_rts' => false,
                ]
            )
        ;
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

        foreach($this->source as $item) {
            $feedItem = new FeedItem();
            $feedItem->setDate(new \DateTime($item->created_at));
            $feedItem->setContent($item->text);
            $feedItem->setPlatform('twitter');
            $feedItem->setPlatformImage('https://pbs.twimg.com/profile_images/1274682711/strayobject-icon.jpg');
            $feedItem->setPlatformLink('https://twitter.com/strayobject/status/'.$item->id_str);
            $this->feed[(string) (new \DateTime($item->created_at))->format('U').'-twitter'] = $feedItem;
        }
    }

    public function when(callable $func, $data = null): TwitterFeedProvider
    {
        if (!empty($this->feed)) {
            $func($this->error, $this->feed, $data);
        } else {
            $this->whens[] = [$func, $data];
        }

        return $this;
    }

    public function watch(callable $func, $data = null): TwitterFeedProvider
    {
        if (!isset($this->feed)) {
            $this->watchers[] = [$func, $data];
        }

        return $this;
    }
}
