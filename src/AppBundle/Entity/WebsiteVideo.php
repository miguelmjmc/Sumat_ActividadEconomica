<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WebsiteVideo
 *
 * @ORM\Table(name="website_video")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WebsiteVideoRepository")
 */
class WebsiteVideo
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
     * @return WebsiteVideo
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
     * @return WebsiteVideo
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
}
