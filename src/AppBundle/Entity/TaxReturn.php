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
     * @Assert\NotBlank
     * @Assert\Range(min = 0, max = 9999999999.99)
     *
     * @ORM\Column(name="taxUnit", type="decimal", precision=20, scale=2)
     */
    private $taxUnit;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 0, max = 9999999999.99)
     *
     * @ORM\Column(name="taxFine", type="decimal", precision=20, scale=2)
     */
    private $taxFine;

    /**
     * @var string
     *
     * @Assert\Length(min = 0, max = 50)
     *
     * @ORM\Column(name="paymentMethodComment", type="string", length=255, nullable=true)
     */
    private $paymentMethodComment;

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
     * @var PaymentMethod
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="PaymentMethod", inversedBy="taxReturn")
     */
    private $paymentMethod;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxReturnEconomicActivity = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getTaxFineFormatted()
    {
        if ($this->taxFine) {
            return $this->taxFine.' %';
        }

        return '0.00 %';
    }

    /**
     * @return string
     */
    public function getTaxFineAmount()
    {
        return (($this->getSubtotal() * $this->taxFine) / 100);
    }

    /**
     * @return string
     */
    public function getTaxFineAmountFormatted()
    {
        return 'Bs. '.number_format($this->getTaxFineAmount(), 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getSubtotal()
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
    public function getSubtotalFormatted()
    {
        return 'Bs. '.number_format($this->getSubtotal(), 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return (double)$this->getSubtotal() + (double)$this->getTaxFineAmount();
    }

    /**
     * @return string
     */
    public function getTotalFormatted()
    {
        return 'Bs. '.number_format($this->getTotal(), 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isLatePayment()
    {
        $deadline = clone $this->getDate();
        $deadline->modify('next month + 15 day midnight');

        if ($deadline > new $this->createdAt) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getPastDueDays()
    {
        $deadline = clone $this->getDate();
        $deadline->modify('next month + 15 day midnight');

        return (new \DateTime('now'))->diff($deadline)->days;
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
     * Set taxFine
     *
     * @param string $taxFine
     *
     * @return TaxReturn
     */
    public function setTaxFine($taxFine)
    {
        $this->taxFine = $taxFine;

        return $this;
    }

    /**
     * Get taxFine
     *
     * @return string
     */
    public function getTaxFine()
    {
        return $this->taxFine;
    }

    /**
     * Set paymentMethodComment
     *
     * @param string $paymentMethodComment
     *
     * @return TaxReturn
     */
    public function setPaymentMethodComment($paymentMethodComment)
    {
        $this->paymentMethodComment = $paymentMethodComment;

        return $this;
    }

    /**
     * Get paymentMethodComment
     *
     * @return string
     */
    public function getPaymentMethodComment()
    {
        return $this->paymentMethodComment;
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

    /**
     * Set paymentMethod
     *
     * @param \AppBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return TaxReturn
     */
    public function setPaymentMethod(\AppBundle\Entity\PaymentMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return \AppBundle\Entity\PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}
