<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebsiteSettings
 *
 * @ORM\Table(name="website_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WebsiteSettingsRepository")
 */
class WebsiteSettings
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=13, scale=10)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=13, scale=10)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="email1", type="string", length=255)
     */
    private $email1;

    /**
     * @var string
     *
     * @ORM\Column(name="email2", type="string", length=255)
     */
    private $email2;

    /**
     * @var string
     *
     * @ORM\Column(name="phone1", type="string", length=255)
     */
    private $phone1;

    /**
     * @var string
     *
     * @ORM\Column(name="phone2", type="string", length=255)
     */
    private $phone2;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255)
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=255)
     */
    private $twitter;

    /**
     * @var string
     *
     * @ORM\Column(name="instagram", type="string", length=255)
     */
    private $instagram;

    /**
     * @var string
     *
     * @ORM\Column(name="youtube", type="string", length=255)
     */
    private $youtube;


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
     * Set title.
     *
     * @param string $title
     *
     * @return WebsiteSettings
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return WebsiteSettings
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
     * Set latitude.
     *
     * @param string $latitude
     *
     * @return WebsiteSettings
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string $longitude
     *
     * @return WebsiteSettings
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set email1.
     *
     * @param string $email1
     *
     * @return WebsiteSettings
     */
    public function setEmail1($email1)
    {
        $this->email1 = $email1;

        return $this;
    }

    /**
     * Get email1.
     *
     * @return string
     */
    public function getEmail1()
    {
        return $this->email1;
    }

    /**
     * Set email2.
     *
     * @param string $email2
     *
     * @return WebsiteSettings
     */
    public function setEmail2($email2)
    {
        $this->email2 = $email2;

        return $this;
    }

    /**
     * Get email2.
     *
     * @return string
     */
    public function getEmail2()
    {
        return $this->email2;
    }

    /**
     * Set phone1.
     *
     * @param string $phone1
     *
     * @return WebsiteSettings
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * Get phone1.
     *
     * @return string
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * Set phone2.
     *
     * @param string $phone2
     *
     * @return WebsiteSettings
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * Get phone2.
     *
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set facebook.
     *
     * @param string $facebook
     *
     * @return WebsiteSettings
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook.
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter.
     *
     * @param string $twitter
     *
     * @return WebsiteSettings
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter.
     *
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set instagram.
     *
     * @param string $instagram
     *
     * @return WebsiteSettings
     */
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * Get instagram.
     *
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * Set youtube.
     *
     * @param string $youtube
     *
     * @return WebsiteSettings
     */
    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;

        return $this;
    }

    /**
     * Get youtube.
     *
     * @return string
     */
    public function getYoutube()
    {
        return $this->youtube;
    }
}
