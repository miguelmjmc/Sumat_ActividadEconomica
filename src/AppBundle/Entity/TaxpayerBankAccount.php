<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TaxpayerBankAccount
 *
 * @ORM\Table(name="taxpayer_bank_account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxpayerBankAccountRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"number", "taxpayer"})
 */
class TaxpayerBankAccount
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
     * @Assert\Length(min = 24, max = 24)
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min = 4, max = 20)
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min = 11, max = 12)
     *
     * @ORM\Column(name="dni", type="string", length=255)
     */
    private $dni;

    /**
     * @var string
     *
     * @Assert\Length(min = 0, max = 1000)
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var BankAccountType
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="BankAccountType", inversedBy="taxpayerBankAccount")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bankAccountType;

    /**
     * @var Taxpayer
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="Taxpayer", inversedBy="taxpayerBankAccount")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taxpayer;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TaxpayerPointOfSale", mappedBy="taxpayerBankAccount")
     */
    private $taxpayerPointOfSale;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TaxpayerBioPayment", mappedBy="taxpayerBankAccount")
     */
    private $taxpayerBioPayment;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxpayerPointOfSale = new \Doctrine\Common\Collections\ArrayCollection();
        $this->taxpayerBioPayment = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set number.
     *
     * @param string $number
     *
     * @return TaxpayerBankAccount
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return TaxpayerBankAccount
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
     * Set dni.
     *
     * @param string $dni
     *
     * @return TaxpayerBankAccount
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni.
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return TaxpayerBankAccount
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set bankAccountType.
     *
     * @param \AppBundle\Entity\BankAccountType $bankAccountType
     *
     * @return TaxpayerBankAccount
     */
    public function setBankAccountType(\AppBundle\Entity\BankAccountType $bankAccountType)
    {
        $this->bankAccountType = $bankAccountType;

        return $this;
    }

    /**
     * Get bankAccountType.
     *
     * @return \AppBundle\Entity\BankAccountType
     */
    public function getBankAccountType()
    {
        return $this->bankAccountType;
    }

    /**
     * Set taxpayer.
     *
     * @param \AppBundle\Entity\Taxpayer $taxpayer
     *
     * @return TaxpayerBankAccount
     */
    public function setTaxpayer(\AppBundle\Entity\Taxpayer $taxpayer)
    {
        $this->taxpayer = $taxpayer;

        return $this;
    }

    /**
     * Get taxpayer.
     *
     * @return \AppBundle\Entity\Taxpayer
     */
    public function getTaxpayer()
    {
        return $this->taxpayer;
    }

    /**
     * Add taxpayerPointOfSale.
     *
     * @param \AppBundle\Entity\TaxpayerPointOfSale $taxpayerPointOfSale
     *
     * @return TaxpayerBankAccount
     */
    public function addTaxpayerPointOfSale(\AppBundle\Entity\TaxpayerPointOfSale $taxpayerPointOfSale)
    {
        $this->taxpayerPointOfSale[] = $taxpayerPointOfSale;

        return $this;
    }

    /**
     * Remove taxpayerPointOfSale.
     *
     * @param \AppBundle\Entity\TaxpayerPointOfSale $taxpayerPointOfSale
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTaxpayerPointOfSale(\AppBundle\Entity\TaxpayerPointOfSale $taxpayerPointOfSale)
    {
        return $this->taxpayerPointOfSale->removeElement($taxpayerPointOfSale);
    }

    /**
     * Get taxpayerPointOfSale.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxpayerPointOfSale()
    {
        return $this->taxpayerPointOfSale;
    }

    /**
     * Add taxpayerBioPayment.
     *
     * @param \AppBundle\Entity\TaxpayerBioPayment $taxpayerBioPayment
     *
     * @return TaxpayerBankAccount
     */
    public function addTaxpayerBioPayment(\AppBundle\Entity\TaxpayerBioPayment $taxpayerBioPayment)
    {
        $this->taxpayerBioPayment[] = $taxpayerBioPayment;

        return $this;
    }

    /**
     * Remove taxpayerBioPayment.
     *
     * @param \AppBundle\Entity\TaxpayerBioPayment $taxpayerBioPayment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTaxpayerBioPayment(\AppBundle\Entity\TaxpayerBioPayment $taxpayerBioPayment)
    {
        return $this->taxpayerBioPayment->removeElement($taxpayerBioPayment);
    }

    /**
     * Get taxpayerBioPayment.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxpayerBioPayment()
    {
        return $this->taxpayerBioPayment;
    }
}
