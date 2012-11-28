<?php

namespace Itc\AdminBundle\Entity\Translation;

use Doctrine\ORM\Mapping as ORM;

/**
 * Itc\AdminBundle\Entity\Translation\TranslationNamespace
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TranslationNamespace
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $namespace
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $namespace;
    
    /**
     * @var string $bundle 
     * 
     * @ORM\Column(name="bundle", type="string", length=255, nullable=true)
     */
    private $bundle;
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
     * Set namespace
     *
     * @param string $namespace
     * @return TranslationNamespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    
        return $this;
    }

    /**
     * Get namespace
     *
     * @return string 
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set bundle
     *
     * @param string $bundle
     * @return TranslationNamespace
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    
        return $this;
    }

    /**
     * Get bundle
     *
     * @return string 
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}