<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Interfaces\RevenueModel;

/**
 * @ORM\Entity(repositoryClass="TimePassRepository")
 * @ORM\Table(name="timepasses", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="voucher_code_idx", columns={
 *          "voucher_code"
 *      })
 * })
 */
class TimePass extends AbstractEntity implements RevenueModel
{
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     */
    protected $category;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $time_valid;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    protected $voucher_code;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    protected $revenue_model;


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
        return $this->getName();
    }


    /**
     * Set name
     *
     * @param string $name
     * @return TimePass
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
     * Set description
     *
     * @param string $description
     * @return TimePass
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return TimePass
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
     * Set time_valid
     *
     * @param integer $timeValid
     * @return TimePass
     */
    public function setTimeValid($timeValid)
    {
        $this->time_valid = $timeValid;

        return $this;
    }

    /**
     * Get time_valid
     *
     * @return integer
     */
    public function getTimeValid()
    {
        return $this->time_valid;
    }

    /**
     * Set voucher_code
     *
     * @param string $voucherCode
     * @return TimePass
     */
    public function setVoucherCode($voucherCode)
    {
        $this->voucher_code = $voucherCode;

        return $this;
    }

    /**
     * Get voucher_code
     *
     * @return string
     */
    public function getVoucherCode()
    {
        return $this->voucher_code;
    }

    /**
     * Set revenue_model
     *
     * @param string $revenueModel
     * @return TimePass
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
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     * @return TimePass
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
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
