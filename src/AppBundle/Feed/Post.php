<?php

namespace AppBundle\Feed;

use Eko\FeedBundle\Item\Writer\ItemInterface;

class Post implements ItemInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var \DateTime
     */
    protected $pubDate;


    /**
     * @param string $title
     * @param string $description
     * @param string $link
     * @param \DateTime $pubDate
     */
    public function __construct($title, $description, $link, \DateTime $pubDate)
    {
        $this->title        = $title;
        $this->description  = $description;
        $this->link         = $link;
        $this->pubDate      = $pubDate;
    }


    /**
     * This method returns feed item title
     *
     * @return string
     */
    public function getFeedItemTitle()
    {
        return $this->title;
    }

    /**
     * This method returns feed item description (or content)
     *
     * @return string
     */
    public function getFeedItemDescription()
    {
        return $this->description;
    }

    /**
     * This method returns feed item URL link
     *
     * @return string
     */
    public function getFeedItemLink()
    {
        return $this->link;
    }

    /**
     * This method returns item publication date
     *
     * @return \DateTime
     */
    public function getFeedItemPubDate()
    {
        return $this->pubDate;
    }

}
