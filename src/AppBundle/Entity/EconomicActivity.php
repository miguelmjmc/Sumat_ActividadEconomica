<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EconomicActivity
 *
 * @ORM\Table(name="economic_activity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EconomicActivityRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("code")
 * @UniqueEntity("name")
 */
class EconomicActivity
{
    use LogTrait;

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
     * @Assert\Length(min = 3, max = 10)
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min = 10, max = 50)
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * @var  ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Taxpayer", mappedBy="economicActivity")
     */
    private $taxpayer;

    /**
     * @var  ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TaxReturnEconomicActivity", mappedBy="economicActivity")
     */
    private $taxReturnEconomicActivity;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxpayer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->taxReturnEconomicActivity = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->code.': '.$this->getName();
    }

    /**
     * @return string
     */
    public function getMinimumTaxableFormatted()
    {
        return number_format($this->minimumTaxable, 2).' UT';
    }

    /**
     * @return string
     */
    public function getAliquotFormatted()
    {
        return number_format($this->aliquot, 2).' %';
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
     * Set code
     *
     * @param string $code
     *
     * @return EconomicActivity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return EconomicActivity
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
     * Set aliquot
     *
     * @param string $aliquot
     *
     * @return EconomicActivity
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
     * @return EconomicActivity
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
     * Add taxpayer
     *
     * @param \AppBundle\Entity\Taxpayer $taxpayer
     *
     * @return EconomicActivity
     */
    public function addTaxpayer(\AppBundle\Entity\Taxpayer $taxpayer)
    {
        $this->taxpayer[] = $taxpayer;

        return $this;
    }

    /**
     * Remove taxpayer
     *
     * @param \AppBundle\Entity\Taxpayer $taxpayer
     */
    public function removeTaxpayer(\AppBundle\Entity\Taxpayer $taxpayer)
    {
        $this->taxpayer->removeElement($taxpayer);
    }

    /**
     * Get taxpayer
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxpayer()
    {
        return $this->taxpayer;
    }

    /**
     * Add taxReturnEconomicActivity
     *
     * @param \AppBundle\Entity\TaxReturnEconomicActivity $taxReturnEconomicActivity
     *
     * @return EconomicActivity
     */
    public function addTaxReturnEconomicActivity(\AppBundle\Entity\TaxReturnEconomicActivity $taxReturnEconomicActivity)
    {
        $this->taxReturnEconomicActivity[] = $taxReturnEconomicActivity;

        return $this;
    }

    /**
     * Remove taxReturnEconomicActivity
     *
     * @param \AppBundle\Entity\TaxReturnEconomicActivity $taxReturnEconomicActivity
     */
    public function removeTaxReturnEconomicActivity(\AppBundle\Entity\TaxReturnEconomicActivity $taxReturnEconomicActivity)
    {
        $this->taxReturnEconomicActivity->removeElement($taxReturnEconomicActivity);
    }

    /**
     * Get taxReturnEconomicActivity
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxReturnEconomicActivity()
    {
        return $this->taxReturnEconomicActivity;
    }
}
