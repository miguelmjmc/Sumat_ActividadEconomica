<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TaxReturn
 *
 * @ORM\Table(name="tax_return")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxReturnRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"taxpayer", "date"})
 */
class TaxReturn
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
     * @var \DateTime
     *
     * @Assert\NotBlank
     * @Assert\Date
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @Assert\Range(min = 0, max = 9999999999.99)
     *
     * @ORM\Column(name="taxUnit", type="decimal", precision=20, scale=2)
     */
    private $taxUnit;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime
     *
     * @ORM\Column(name="declaredAt", type="datetime", nullable=true)
     */
    private $declaredAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime
     *
     * @ORM\Column(name="paidAt", type="datetime", nullable=true)
     */
    private $paidAt;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime
     *
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var Taxpayer
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="Taxpayer", inversedBy="taxReturn")
     */
    private $taxpayer;

    /**
     * @var ArrayCollection
     *
     * @Assert\Count(min = 1)
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="TaxReturnEconomicActivity", mappedBy="taxReturn", cascade={"all"})
     */
    private $taxReturnEconomicActivity;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxReturnEconomicActivity = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        if ($this->updatedAt) {
            return $this->updatedAt;
        }

        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getAmountToPay()
    {
       $total = 0;

       /** @var TaxReturnEconomicActivity $taxReturnEconomicActivity */
        foreach ($this->getTaxReturnEconomicActivity() as $taxReturnEconomicActivity) {
            $total += (double)$taxReturnEconomicActivity->getAmountToPay();
        }

        return $total;
    }

    /**
     * @return string
     */
    public function getAmountToPayFormatted()
    {
        return number_format($this->getAmountToPay(), 2).' Bs';
    }

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->id;
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TaxReturn
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set taxUnit
     *
     * @param string $taxUnit
     *
     * @return TaxReturn
     */
    public function setTaxUnit($taxUnit)
    {
        $this->taxUnit = $taxUnit;

        return $this;
    }

    /**
     * Get taxUnit
     *
     * @return string
     */
    public function getTaxUnit()
    {
        return $this->taxUnit;
    }

    /**
     * Set declaredAt
     *
     * @param \DateTime $declaredAt
     *
     * @return TaxReturn
     */
    public function setDeclaredAt($declaredAt)
    {
        $this->declaredAt = $declaredAt;

        return $this;
    }

    /**
     * Get declaredAt
     *
     * @return \DateTime
     */
    public function getDeclaredAt()
    {
        return $this->declaredAt;
    }

    /**
     * Set paidAt
     *
     * @param \DateTime $paidAt
     *
     * @return TaxReturn
     */
    public function setPaidAt($paidAt)
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    /**
     * Get paidAt
     *
     * @return \DateTime
     */
    public function getPaidAt()
    {
        return $this->paidAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return TaxReturn
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set taxpayer
     *
     * @param \AppBundle\Entity\Taxpayer $taxpayer
     *
     * @return TaxReturn
     */
    public function setTaxpayer(\AppBundle\Entity\Taxpayer $taxpayer = null)
    {
        $this->taxpayer = $taxpayer;

        return $this;
    }

    /**
     * Get taxpayer
     *
     * @return \AppBundle\Entity\Taxpayer
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
     * @return TaxReturn
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
    public function removeTaxReturnEconomicActivity(
        \AppBundle\Entity\TaxReturnEconomicActivity $taxReturnEconomicActivity
    ) {
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
