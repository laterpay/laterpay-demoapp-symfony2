<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PostRepository")
 * @ORM\Table(name="posts")
 */
class Post extends AbstractEntity
{
    const STATUS_NEW    = 0;

    const RM_PPU    = 'ppu';
    const RM_SIS    = 'sis';

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $category;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $teaser_content;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    protected $status = self::STATUS_NEW;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $price;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $published_at;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    protected $revenue_model;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->published_at = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return bool
     */
    public function getPublished()
    {
        return $this->getPublishedAt() !== null;
    }

    /**
     * @param bool $value
     * @return \AppBundle\Entity\Post
     */
    public function setPublished($value)
    {
        $this->setPublishedAt($value ? new \DateTime() : null);

        return $this;
    }

    /**
     * @param int $wordsCount
     * @param string $suffix
     * @return string
     */
    public function getTruncatedContent($wordsCount = 60, $suffix = '...')
    {
        $content = $this->getContent();

        $words = explode(' ', $content);

        if (sizeof($words) > $wordsCount) {
            $content = implode(' ', array_slice($words, 0, $wordsCount)) . $suffix;
        }

        return $content;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set teaser_content
     *
     * @param string $teaserContent
     * @return Post
     */
    public function setTeaserContent($teaserContent)
    {
        $this->teaser_content = $teaserContent;

        return $this;
    }

    /**
     * Get teaser_content
     *
     * @return string
     */
    public function getTeaserContent()
    {
        return $this->teaser_content;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Post
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Post
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set published_at
     *
     * @param \DateTime $publishedAt
     * @return Post
     */
    public function setPublishedAt($publishedAt)
    {
        $this->published_at = $publishedAt;

        return $this;
    }

    /**
     * Get published_at
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * Set revenue_model
     *
     * @param string $revenueModel
     * @return Post
     */
    public function setRevenueModel($revenueModel)
    {
        $this->revenue_model = $revenueModel;

        return $this;
    }

    /**
     * Get revenue_model
     *
     * @return string
     */
    public function getRevenueModel()
    {
        return $this->revenue_model;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Post
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     * @return Post
     */
    public function setCategory(\AppBundle\Entity\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
