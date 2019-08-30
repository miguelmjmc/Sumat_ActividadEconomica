<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
{
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
     * @Assert\Range(min = 0, max = 9999999999.99)
     *
     * @ORM\Column(name="taxUnit", type="decimal", precision=20, scale=2)
     */
    private $taxUnit;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 0, max = 100)
     *
     * @ORM\Column(name="taxFine1", type="decimal", precision=10, scale=2)
     */
    private $taxFine1;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 0, max = 100)
     *
     * @ORM\Column(name="taxFine2", type="decimal", precision=10, scale=2)
     */
    private $taxFine2;

    /**
     * @var string
     *
     * @Assert\File(maxSize = "2048k", mimeTypes = {"image/png", "image/jpeg"})
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;


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
     * Set taxUnit.
     *
     * @param string $taxUnit
     *
     * @return Settings
     */
    public function setTaxUnit($taxUnit)
    {
        $this->taxUnit = $taxUnit;

        return $this;
    }

    /**
     * Get taxUnit.
     *
     * @return string
     */
    public function getTaxUnit()
    {
        return $this->taxUnit;
    }

    /**
     * Set taxFine1.
     *
     * @param string $taxFine1
     *
     * @return Settings
     */
    public function setTaxFine1($taxFine1)
    {
        $this->taxFine1 = $taxFine1;

        return $this;
    }

    /**
     * Get taxFine1.
     *
     * @return string
     */
    public function getTaxFine1()
    {
        return $this->taxFine1;
    }

    /**
     * Set taxFine2.
     *
     * @param string $taxFine2
     *
     * @return Settings
     */
    public function setTaxFine2($taxFine2)
    {
        $this->taxFine2 = $taxFine2;

        return $this;
    }

    /**
     * Get taxFine2.
     *
     * @return string
     */
    public function getTaxFine2()
    {
        return $this->taxFine2;
    }

    /**
     * Set img.
     *
     * @param string|null $img
     *
     * @return Settings
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
}
