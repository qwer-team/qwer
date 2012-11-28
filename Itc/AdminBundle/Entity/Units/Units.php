<?php

namespace Itc\AdminBundle\Entity\Units;
use Itc\AdminBundle\Entity\TranslatableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Itc\AdminBundle\Entity\Units\Units
 *
 * @ORM\Entity()
 * @ORM\Table()
 */
class Units extends TranslatableEntity
{
    /**
     * @var integer
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
    protected $title;

    /**
    * @ORM\OneToMany(
    *     targetEntity="UnitsTranslation",
    *     mappedBy="translatable",
    *     cascade={"persist"}
    * )
    */
    protected $translations;
    
    protected $fields = array('title');
     /*
     * @ORM\OneToMany(
     *     targetEntity="Itc\AdminBundle\Entity\Product\Product",
     *     mappedBy="units",
     *     cascade={"persist"}
     * )
     */
    protected $products;

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
     * Set title
     *
     * @param string $title
     * @return Units
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return Units
     */
/*    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }
*/
    /**
     * Get value
     *
     * @return integer 
     */
/*    public function getValue()
    {
        return $this->value;
    }
  */  /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add translations
     *
     * @param \Itc\AdminBundle\Entity\Units\UnitsTranslation $translations
     * @return Units
     */
    public function addTranslation(\Itc\AdminBundle\Entity\Units\UnitsTranslation $translations)
    {
        $this->translations[] = $translations;
    
        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Itc\AdminBundle\Entity\Units\UnitsTranslation $translations
     */
    public function removeTranslation(\Itc\AdminBundle\Entity\Units\UnitsTranslation $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}