<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TaxpayerBioPayment
 *
 * @ORM\Table(name="taxpayer_bio_payment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxpayerBioPaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TaxpayerBioPayment
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
     * @Assert\Length(min = 4, max = 20)
     *
     * @ORM\Column(name="serial", type="string", length=255)
     */
    private $serial;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min = 4, max = 20)
     *
     * @ORM\Column(name="brand", type="string", length=255)
     */
    private $brand;

    /**
     * @var string|null
     *
     * @Assert\Length(min = 0, max = 1000)
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var TaxpayerBankAccount
     *
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="TaxpayerBankAccount", inversedBy="taxpayerBioPayment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taxpayerBankAccount;


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
     * Set serial.
     *
     * @param string $serial
     *
     * @return TaxpayerBioPayment
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial.
     *
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set brand.
     *
     * @param string $brand
     *
     * @return TaxpayerBioPayment
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand.
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return TaxpayerBioPayment
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
     * Set taxpayerBankAccount.
     *
     * @param \AppBundle\Entity\TaxpayerBankAccount $taxpayerBankAccount
     *
     * @return TaxpayerBioPayment
     */
    public function setTaxpayerBankAccount(\AppBundle\Entity\TaxpayerBankAccount $taxpayerBankAccount)
    {
        $this->taxpayerBankAccount = $taxpayerBankAccount;

        return $this;
    }

    /**
     * Get taxpayerBankAccount.
     *
     * @return \AppBundle\Entity\TaxpayerBankAccount
     */
    public function getTaxpayerBankAccount()
    {
        return $this->taxpayerBankAccount;
    }
}
