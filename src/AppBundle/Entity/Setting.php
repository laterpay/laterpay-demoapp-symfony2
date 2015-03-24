<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="key_idx", columns={
 *          "key"
 *      })
 * })
 */
class Setting extends AbstractEntity
{
    /**
     * @ORM\Column(name="`key`", type="string", length=45)
     */
    protected $key;

    /**
     * @ORM\Column(type="text")
     */
    protected $value;


    /**
     * Constructor
     */
    public function __construct()
    {
        // nothing
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getKey();
    }


    /**
     * Set key
     *
     * @param string $key
     * @return Setting
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
