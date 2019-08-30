<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Taxpayer
 *
 * @ORM\Table(name="taxpayer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxpayerRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"rif", "name"})
 */
class Taxpayer
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
     * @Assert\Length(min = 12, max = 12)
     *
     * @ORM\Column(name="rif", type="string", length=255)
     */
    private $rif;

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
     * @Assert\Length(min = 10, max = 200)
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string|null
     *
     * @Assert\Email
     * @Assert\Length(min = 10, max = 50)
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @Assert\Length(min = 15, max = 15)
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank
     * @Assert\Date
     * @Assert\Range(min="2019-01-01", max="2030-12-31")
     *
     * @ORM\Column(name="startTaxReturn", type="date")
     */
    private $startTaxReturn;

    /**
     * @var string|null
     *
     * @Assert\File(maxSize = "2048k", mimeTypes = {"image/png", "image/jpeg"})
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="EconomicActivity", inversedBy="taxpayer")
     */
    private $economicActivity;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TaxReturn", mappedBy="taxpayer")
     */
    private $taxReturn;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TaxpayerBankAccount", mappedBy="taxpayer")
     */
    private $taxpayerBankAccount;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->economicActivity = new \Doctrine\Common\Collections\ArrayCollection();
        $this->taxReturn = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->rif.': '.$this->name;
    }

    /**
     * @return TaxReturn
     */
    public function getLastTaxReturn()
    {
        $lastTaxReturn = null;

        if (0 < $this->getTaxReturn()->count()) {

            /** @var TaxReturn $lastTaxReturn */
            $lastTaxReturn = $this->getTaxReturn()->get(0);

            /** @var TaxReturn $taxReturn */
            foreach ($this->getTaxReturn() as $taxReturn) {
                if ($lastTaxReturn->getDate() < $taxReturn->getDate()) {
                    $lastTaxReturn = $taxReturn;
                }
            }
        }

        return $lastTaxReturn;
    }

    /**
     * @return bool
     */
    public function isSolvent()
    {
        $date = clone $this->startTaxReturn;
        $date->modify('first day of next month midnight');

        if (new \DateTime('now') < $date) {
            return true;
        }

        $lastTaxReturn = $this->getLastTaxReturn();

        if ($lastTaxReturn instanceof TaxReturn) {
            $date = clone $lastTaxReturn->getDate();
            $date->modify('first day of next month midnight');
            $date->modify('+1 month');

            if (new \DateTime('now') < $date) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isGracePeriod()
    {
        if ($this->isSolvent()) {
            return false;
        }

        $date = clone $this->startTaxReturn;
        $date->modify('first day of next month midnight');
        $date->modify('+15 days');

        if (new \DateTime('now') < $date) {
            return true;
        }

        $lastTaxReturn = $this->getLastTaxReturn();

        if ($lastTaxReturn instanceof TaxReturn) {
            $date = clone $lastTaxReturn->getDate();
            $date->modify('first day of next month midnight');
            $date->modify('+1 month');
            $date->modify('+15 days');


            if (new \DateTime('now') < $date) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isInsolvent()
    {
        if ($this->isSolvent() || $this->isGracePeriod()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if ($this->isSolvent()) {
            return '<span class="label label-success">Solvente</span>';
        }

        if ($this->isGracePeriod()) {
            return '<span class="label label-warning">Periodo de Gracia</span>';
        }

        return '<span class="label label-danger">Inolvente</span>';
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        if ($this->isSolvent()) {
            return 2;
        }

        if ($this->isGracePeriod()) {
            return 1;
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getDeclaredAmount()
    {
        $total = 0;

        /** @var TaxReturn $taxReturn */
        foreach ($this->taxReturn as $taxReturn) {
            $total += (float)$taxReturn->getDeclaredAmount();
        }

        return (float)$total;
    }

    /**
     * @return string
     */
    public function getDeclaredAmountFormatted()
    {
        return 'Bs. '.number_format($this->getDeclaredAmount(), 2, ',', '.');
    }

    /**
     * @return float
     */
    public function getEconomicActivityAmount()
    {
        $total = 0;

        /** @var TaxReturn $taxReturn */
        foreach ($this->taxReturn as $taxReturn) {
            $total += (float)$taxReturn->getEconomicActivityAmount();
        }

        return (float)$total;
    }

    /**
     * @return string
     */
    public function getEconomicActivityAmountFormatted()
    {
        return 'Bs. '.number_format($this->getEconomicActivityAmount(), 2, ',', '.');
    }

    /**
     * @return float
     */
    public function getTaxFineAmount()
    {
        $total = 0;

        /** @var TaxReturn $taxReturn */
        foreach ($this->taxReturn as $taxReturn) {
            $total += (float)$taxReturn->getTaxFineAmount();
        }

        return (float)$total;
    }

    /**
     * @return string
     */
    public function getTaxFineAmountFormatted()
    {
        return 'Bs. '.number_format($this->getTaxFineAmount(), 2, ',', '.');
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        $total = 0;

        /** @var TaxReturn $taxReturn */
        foreach ($this->taxReturn as $taxReturn) {
            $total += (float)$taxReturn->getTotalAmount();
        }

        return (float)$total;
    }

    /**
     * @return string
     */
    public function getTotalAmountFormatted()
    {
        return 'Bs. '.number_format($this->getTotalAmount(), 2, ',', '.');
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rif.
     *
     * @param string $rif
     *
     * @return Taxpayer
     */
    public function setRif($rif)
    {
        $this->rif = $rif;

        return $this;
    }

    /**
     * Get rif.
     *
     * @return string
     */
    public function getRif()
    {
        return $this->rif;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Taxpayer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Taxpayer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Taxpayer
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return Taxpayer
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set startTaxReturn.
     *
     * @param \DateTime $startTaxReturn
     *
     * @return Taxpayer
     */
    public function setStartTaxReturn($startTaxReturn)
    {
        $this->startTaxReturn = $startTaxReturn;

        return $this;
    }

    /**
     * Get startTaxReturn.
     *
     * @return \DateTime
     */
    public function getStartTaxReturn()
    {
        return $this->startTaxReturn;
    }

    /**
     * Set img.
     *
     * @param string|null $img
     *
     * @return Taxpayer
     */
    public function setImg($img = null)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img.
     *
     * @return string|null
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Add economicActivity.
     *
     * @param \AppBundle\Entity\EconomicActivity $economicActivity
     *
     * @return Taxpayer
     */
    public function addEconomicActivity(\AppBundle\Entity\EconomicActivity $economicActivity)
    {
        $this->economicActivity[] = $economicActivity;

        return $this;
    }

    /**
     * Remove economicActivity.
     *
     * @param \AppBundle\Entity\EconomicActivity $economicActivity
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEconomicActivity(\AppBundle\Entity\EconomicActivity $economicActivity)
    {
        return $this->economicActivity->removeElement($economicActivity);
    }

    /**
     * Get economicActivity.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEconomicActivity()
    {
        return $this->economicActivity;
    }

    /**
     * Add taxReturn.
     *
     * @param \AppBundle\Entity\TaxReturn $taxReturn
     *
     * @return Taxpayer
     */
    public function addTaxReturn(\AppBundle\Entity\TaxReturn $taxReturn)
    {
        $this->taxReturn[] = $taxReturn;

        return $this;
    }

    /**
     * Remove taxReturn.
     *
     * @param \AppBundle\Entity\TaxReturn $taxReturn
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTaxReturn(\AppBundle\Entity\TaxReturn $taxReturn)
    {
        return $this->taxReturn->removeElement($taxReturn);
    }

    /**
     * Get taxReturn.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxReturn()
    {
        return $this->taxReturn;
    }

    /**
     * Add taxpayerBankAccount.
     *
     * @param \AppBundle\Entity\TaxpayerBankAccount $taxpayerBankAccount
     *
     * @return Taxpayer
     */
    public function addTaxpayerBankAccount(\AppBundle\Entity\TaxpayerBankAccount $taxpayerBankAccount)
    {
        $this->taxpayerBankAccount[] = $taxpayerBankAccount;

        return $this;
    }

    /**
     * Remove taxpayerBankAccount.
     *
     * @param \AppBundle\Entity\TaxpayerBankAccount $taxpayerBankAccount
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTaxpayerBankAccount(\AppBundle\Entity\TaxpayerBankAccount $taxpayerBankAccount)
    {
        return $this->taxpayerBankAccount->removeElement($taxpayerBankAccount);
    }

    /**
     * Get taxpayerBankAccount.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxpayerBankAccount()
    {
        return $this->taxpayerBankAccount;
    }
}
