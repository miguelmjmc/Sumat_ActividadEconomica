<?php

namespace AppBundle\Entity;

use AppBundle\Utils\LogTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    use LogTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @Assert\NotBlank
     * @Assert\Length(min = 4, max = 20)
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @Assert\NotBlank
     * @Assert\Length(min = 4, max = 20)
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @Assert\File(maxSize = "2048k", mimeTypes = {"image/png", "image/jpeg"})
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HistorySession", mappedBy="user")
     */
    private $historySession;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HistoryRequest", mappedBy="user")
     */
    private $historyRequest;


    public function __construct()
    {
        parent::__construct();

        $this->historySession= new \Doctrine\Common\Collections\ArrayCollection();
        $this->historyRequest = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->name.' '.$this->lastName;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if ($this->isEnabled()) {
            return '<span class="label label-success" title="Habilitado">Habilitado</span>';
        }

        return '<span class="label label-danger" title="Desabilitado">Desabilitado</span>';
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return User
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastName.
     *
     * @param string|null $lastName
     *
     * @return User
     */
    public function setLastName($lastName = null)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set img.
     *
     * @param string|null $img
     *
     * @return User
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

    /**
     * Add historySession.
     *
     * @param \AppBundle\Entity\HistorySession $historySession
     *
     * @return User
     */
    public function addHistorySession(\AppBundle\Entity\HistorySession $historySession)
    {
        $this->historySession[] = $historySession;

        return $this;
    }

    /**
     * Remove historySession.
     *
     * @param \AppBundle\Entity\HistorySession $historySession
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHistorySession(\AppBundle\Entity\HistorySession $historySession)
    {
        return $this->historySession->removeElement($historySession);
    }

    /**
     * Get historySession.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistorySession()
    {
        return $this->historySession;
    }

    /**
     * Add historyRequest.
     *
     * @param \AppBundle\Entity\HistoryRequest $historyRequest
     *
     * @return User
     */
    public function addHistoryRequest(\AppBundle\Entity\HistoryRequest $historyRequest)
    {
        $this->historyRequest[] = $historyRequest;

        return $this;
    }

    /**
     * Remove historyRequest.
     *
     * @param \AppBundle\Entity\HistoryRequest $historyRequest
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeHistoryRequest(\AppBundle\Entity\HistoryRequest $historyRequest)
    {
        return $this->historyRequest->removeElement($historyRequest);
    }

    /**
     * Get historyRequest.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoryRequest()
    {
        return $this->historyRequest;
    }
}
