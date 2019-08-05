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
     * Constructor
     */
    public function __construct()
    {
        $this->taxReturn = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add taxReturn
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
     * Remove taxReturn
     *
     * @param \AppBundle\Entity\TaxReturn $taxReturn
     */
    public function removeTaxReturn(\AppBundle\Entity\TaxReturn $taxReturn)
    {
        $this->taxReturn->removeElement($taxReturn);
    }

    /**
     * Get taxReturn
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaxReturn()
    {
        return $this->taxReturn;
    }
}
