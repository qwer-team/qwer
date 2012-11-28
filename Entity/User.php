<?php

namespace Itc\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
/**
 * Itc\AdminBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string $status
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;
    /**
     * @var string $tel
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     */
    protected $tel;
    /**
     * @var string $address
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;
    /**
     * @var string $fio
     * @ORM\Column(name="fio", type="string", length=255, nullable=true)
     */
    protected $fio;
    /**
     * @var \DateTime $date_registrate
     *
     * @ORM\Column(name="date_registrate", type="datetime")
     */
    protected $date_registrate;

    
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
     *
     * @return type 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
    /**
     *
     * @return type 
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }
    public function getDateRegistrate()
    {
        return $this->date_registrate;
    }
    public function getPassword()
    {
        return $this->password;
    }
    /**
     *
     * @return type 
     */
    public function getFio()
    {
        return $this->fio;
    }
    /**
     *
     * @return type 
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     *
     * @return type 
     */
    public function getTel()
    {
        return $this->tel;
    }
    /**
     *
     * @return type 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     *
     * @param type $fio 
     */
    public function setFio($fio)
    {
        
         $this->fio=$fio;
    }

    /**
     *
     * @param type $tel 
     */
    public function setTel($tel)
    {
        $this->tel=$tel;
    }
    /**
     *
     * @param type $status 
     */
    public function setStatus($status)
    {
        $this->status=$status;
    }
    
    public function setAddress($address)
    {
        $this->address=$address;
    }

     /**
    * @ORM\PrePersist
    */
    public function dateOfCreate()
    {
        $this->date_registrate = new \DateTime();
    }
}
