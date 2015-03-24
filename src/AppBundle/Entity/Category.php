<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="category", cascade={"all"})
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="TimePass", mappedBy="category", cascade={"all"})
     */
    protected $timepasses;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->post         = new \Doctrine\Common\Collections\ArrayCollection();
        $this->timepasses   = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add posts
     *
     * @param \AppBundle\Entity\Post $posts
     * @return Category
     */
    public function addPost(\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \AppBundle\Entity\Post $posts
     */
    public function removePost(\AppBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add timepasses
     *
     * @param \AppBundle\Entity\TimePass $timepasses
     * @return Category
     */
    public function addTimepass(\AppBundle\Entity\TimePass $timepasses)
    {
        $this->timepasses[] = $timepasses;

        return $this;
    }

    /**
     * Remove timepasses
     *
     * @param \AppBundle\Entity\TimePass $timepasses
     */
    public function removeTimepass(\AppBundle\Entity\TimePass $timepasses)
    {
        $this->timepasses->removeElement($timepasses);
    }

    /**
     * Get timepasses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimepasses()
    {
        return $this->timepasses;
    }
}
