<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TaxpayerPointOfSale
 *
 * @ORM\Table(name="taxpayer_point_of_sale")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxpayerPointOfSaleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TaxpayerPointOfSale
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
     * @var string
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
     * @ORM\ManyToOne(targetEntity="TaxpayerBankAccount", inversedBy="taxpayerPointOfSale")
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
     * @return TaxpayerPointOfSale
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
     * @return TaxpayerPointOfSale
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
     * @return TaxpayerPointOfSale
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
     * @return TaxpayerPointOfSale
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
