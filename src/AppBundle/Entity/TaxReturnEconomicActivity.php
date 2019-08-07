<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TaxReturnEconomicActivity
 *
 * @ORM\Table(name="tax_return_economic_activity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxReturnEconomicActivityRepository")
 * @UniqueEntity(fields={"taxReturn", "economicActivity"})
 */
class TaxReturnEconomicActivity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 0, max = 100)
     *
     * @ORM\Column(name="aliquot", type="decimal", precision=10, scale=2)
     */
    private $aliquot;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 0, max = 9999999999.99)
     *
     * @ORM\Column(name="minimumTaxable", type="decimal", precision=20, scale=2)
     */
    private $minimumTaxable;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 0, max = 9999999999.99)
     *
     * @ORM\Column(name="declaredAmount", type="decimal", precision=20, scale=2)
     */
    private $declaredAmount;

    /**
     * @var TaxReturn
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="TaxReturn", inversedBy="taxReturnEconomicActivity")
     */
    private $taxReturn;

    /**
     * @var EconomicActivity
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="EconomicActivity", inversedBy="taxReturnEconomicActivity")
     */
    private $economicActivity;


    /**
     * @return string
     */
    public function getMinimumTaxableFormatted()
    {
        return number_format($this->minimumTaxable, 2, ',', '.').' UT';
    }

    /**
     * @return string
     */
    public function getAliquotFormatted()
    {
        return number_format($this->aliquot, 2, ',', '.').' %';
    }

    /**
     * @return string
     */
    public function getMinimumTaxableBs()
    {
        return $this->minimumTaxable * $this->getTaxReturn()->getTaxUnit();
    }

    /**
     * @return string
     */
    public function getMinimumTaxableBsFormatted()
    {
        return 'Bs. '.number_format($this->getMinimumTaxableBs(), 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getAmountToPay()
    {
        if ($this->getMinimumTaxableBs() > (($this->declaredAmount * $this->aliquot) / 100)) {
            return $this->getMinimumTaxableBs();
        }

        return ($this->declaredAmount * $this->aliquot) / 100;
    }

    /**
     * @return string
     */
    public function getAmountToPayFormatted()
    {
        return 'Bs. '.number_format($this->getAmountToPay(), 2, ',', '.');
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set aliquot
     *
     * @param string $aliquot
     *
     * @return TaxReturnEconomicActivity
     */
    public function setAliquot($aliquot)
    {
        $this->aliquot = $aliquot;

        return $this;
    }

    /**
     * Get aliquot
     *
     * @return string
     */
    public function getAliquot()
    {
        return $this->aliquot;
    }

    /**
     * Set minimumTaxable
     *
     * @param string $minimumTaxable
     *
     * @return TaxReturnEconomicActivity
     */
    public function setMinimumTaxable($minimumTaxable)
    {
        $this->minimumTaxable = $minimumTaxable;

        return $this;
    }

    /**
     * Get minimumTaxable
     *
     * @return string
     */
    public function getMinimumTaxable()
    {
        return $this->minimumTaxable;
    }

    /**
     * Set declaredAmount
     *
     * @param string $declaredAmount
     *
     * @return TaxReturnEconomicActivity
     */
    public function setDeclaredAmount($declaredAmount)
    {
        $this->declaredAmount = $declaredAmount;

        return $this;
    }

    /**
     * Get declaredAmount
     *
     * @return string
     */
    public function getDeclaredAmount()
    {
        return $this->declaredAmount;
    }

    /**
     * Set taxReturn
     *
     * @param \AppBundle\Entity\TaxReturn $taxReturn
     *
     * @return TaxReturnEconomicActivity
     */
    public function setTaxReturn(\AppBundle\Entity\TaxReturn $taxReturn = null)
    {
        $this->taxReturn = $taxReturn;

        return $this;
    }

    /**
     * Get taxReturn
     *
     * @return \AppBundle\Entity\TaxReturn
     */
    public function getTaxReturn()
    {
        return $this->taxReturn;
    }

    /**
     * Set economicActivity
     *
     * @param \AppBundle\Entity\EconomicActivity $economicActivity
     *
     * @return TaxReturnEconomicActivity
     */
    public function setEconomicActivity(\AppBundle\Entity\EconomicActivity $economicActivity = null)
    {
        $this->economicActivity = $economicActivity;

        return $this;
    }

    /**
     * Get economicActivity
     *
     * @return \AppBundle\Entity\EconomicActivity
     */
    public function getEconomicActivity()
    {
        return $this->economicActivity;
    }
}
