<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PaymentMethod
 *
 * @ORM\Table(name="payment_method")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentMethodRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
 */
class PaymentMethod
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TaxReturn", mappedBy="paymentMethod")
     */
    private $taxReturn;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Taxpayer", mappedBy="paymentMethod")
     */
    private $taxpayer;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taxReturn = new \Doctrine\Common\Collections\ArrayCollection();
        $this->taxpayer = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return PaymentMethod
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
     * Add taxReturn.
     *
     * @param \AppBundle\Entity\TaxReturn $taxReturn
     *
     * @return PaymentMethod
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
     * Add taxpayer.
     *
     * @param \AppBundle\Entity\Taxpayer $taxpayer
     *
     * @return PaymentMethod
     */
    public function addTaxpayer(\AppBundle\Entity\Taxpayer $taxpayer)
    {
        $this->taxpayer[] = $taxpayer;

        return $this;
    }

    /**
     * Remove taxpayer.
     *
     * @param \AppBundle\Entity\Taxpayer $taxpayer
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTaxpayer(\AppBundle\Entity\Taxpayer $taxpayer)
    {
        return $this->taxpayer->removeElement($taxpayer);
    }

    /**
     * Get taxpayer.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxpayer()
    {
        return $this->taxpayer;
    }
}
