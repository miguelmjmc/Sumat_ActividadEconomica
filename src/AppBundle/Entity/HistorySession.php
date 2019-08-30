<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History_Login
 *
 * @ORM\Table(name="history_session")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistorySessionRepository")
 */
class HistorySession
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateLogin", type="datetime")
     */
    private $dateLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateLogout", type="datetime")
     */
    private $dateLogout;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="historySession")
     */
    private $user;


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
     * Set dateLogin.
     *
     * @param \DateTime $dateLogin
     *
     * @return HistorySession
     */
    public function setDateLogin($dateLogin)
    {
        $this->dateLogin = $dateLogin;

        return $this;
    }

    /**
     * Get dateLogin.
     *
     * @return \DateTime
     */
    public function getDateLogin()
    {
        return $this->dateLogin;
    }

    /**
     * Set dateLogout.
     *
     * @param \DateTime $dateLogout
     *
     * @return HistorySession
     */
    public function setDateLogout($dateLogout)
    {
        $this->dateLogout = $dateLogout;

        return $this;
    }

    /**
     * Get dateLogout.
     *
     * @return \DateTime
     */
    public function getDateLogout()
    {
        return $this->dateLogout;
    }

    /**
     * Set ip.
     *
     * @param string $ip
     *
     * @return HistorySession
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User|null $user
     *
     * @return HistorySession
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
