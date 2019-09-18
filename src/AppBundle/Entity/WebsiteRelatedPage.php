<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WebsiteRelatedPage
 *
 * @ORM\Table(name="website_related_page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WebsiteRelatedPageRepository")
 */
class WebsiteRelatedPage
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
     * @Assert\Length(min = 10, max = 50)
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min = 10, max = 200)
     * @Assert\Url(checkDNS = "ANY")
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255)
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
     * Set title.
     *
     * @param string $title
     *
     * @return WebsiteRelatedPage
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
     * Set url.
     *
     * @param string $url
     *
     * @return WebsiteRelatedPage
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set img.
     *
     * @param string $img
     *
     * @return WebsiteRelatedPage
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img.
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }
}
