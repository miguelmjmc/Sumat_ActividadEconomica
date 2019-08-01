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
     * @var string
     *
     * @Assert\Email
     * @Assert\Length(min = 10, max = 50)
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
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
     * @ORM\Column(name="startDateTaxReturn", type="date")
     */
    private $startDateTaxReturn;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rif
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
     * Get rif
     *
     * @return string
     */
    public function getRif()
    {
        return $this->rif;
    }

    /**
     * Set name
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
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
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Taxpayer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Taxpayer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set startDateTaxReturn
     *
     * @param \DateTime $startDateTaxReturn
     *
     * @return Taxpayer
     */
    public function setStartDateTaxReturn($startDateTaxReturn)
    {
        $this->startDateTaxReturn = $startDateTaxReturn;

        return $this;
    }

    /**
     * Get startDateTaxReturn
     *
     * @return \DateTime
     */
    public function getStartDateTaxReturn()
    {
        return $this->startDateTaxReturn;
    }

    /**
     * Add economicActivity
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
     * Remove economicActivity
     *
     * @param \AppBundle\Entity\EconomicActivity $economicActivity
     */
    public function removeEconomicActivity(\AppBundle\Entity\EconomicActivity $economicActivity)
    {
        $this->economicActivity->removeElement($economicActivity);
    }

    /**
     * Get economicActivity
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEconomicActivity()
    {
        return $this->economicActivity;
    }

    /**
     * Add taxReturn
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
