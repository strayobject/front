<?php
declare(strict_types=1);

namespace Front\Feed;

class FeedItem
{
    /**
     * @var DateTime
     */
    private $date;
    /**
     * @var string
     */
    private $title = '';
    /**
     * @var string
     */
    private $content = '';
    /**
     * @var string
     */
    private $platformImage = '';
    /**
     * @var string
     */
    private $platformLink = '';
    /**
     * @var string
     */
    private $platform = '';

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * Gets the value of date.
     *
     * @return DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * Sets the value of date.
     *
     * @param DateTime $date the date
     *
     * @return self
     */
    public function setDate(\DateTime $date): FeedItem
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title): FeedItem
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Sets the value of content.
     *
     * @param string $content the content
     *
     * @return self
     */
    public function setContent($content): FeedItem
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the value of platformImage.
     *
     * @return string
     */
    public function getPlatformImage(): string
    {
        return $this->platformImage;
    }

    /**
     * Sets the value of platformImage.
     *
     * @param string $platformImage the platform image
     *
     * @return self
     */
    public function setPlatformImage($platformImage): FeedItem
    {
        $this->platformImage = $platformImage;

        return $this;
    }

    /**
     * Gets the value of platformLink.
     *
     * @return string
     */
    public function getPlatformLink(): string
    {
        return $this->platformLink;
    }

    /**
     * Sets the value of platformLink.
     *
     * @param string $platformLink the platform link
     *
     * @return self
     */
    public function setPlatformLink($platformLink): FeedItem
    {
        $this->platformLink = $platformLink;

        return $this;
    }

    /**
     * Gets the value of platform.
     *
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * Sets the value of platform.
     *
     * @param string $platform the platform
     *
     * @return self
     */
    public function setPlatform($platform): FeedItem
    {
        $this->platform = $platform;

        return $this;
    }
}
