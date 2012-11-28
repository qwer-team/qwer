<?php

namespace Itc\AdminBundle\Entity;
use Itc\AdminBundle\Entity\TranslatableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

class Content extends TranslatableEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;
    
    /**
     * @var string $translit
     *
     * @ORM\Column(name="translit", type="string", length=255, nullable=true,
     * options={"default" = ""})
     */
    protected $translit;
    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="menu_icon", fileNameProperty="icon")
     *
     * @var File $iconImage
     */
    protected $iconImage;
    /**
     * @var string $icon
     *
     * @ORM\Column(name="icon", type="string", length=123, nullable=true)
     */
    protected $icon; 
    
   
    /**
     * @var string $icon
     *
     * @ORM\Column(name="smallIcon", type="string", length=123, nullable=true)
     */
    protected $smallIcon; 
    
    /**
     * @var integer $kod
     *
     * @ORM\Column(name="kod", type="integer")
     */
    protected $kod;
    
    /**
     * @var text $content
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;
    
    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true,
     * options={"default" = NULL})
     */
    protected $description;
    /**
     * @var text $metaTitle
     * 
     * @ORM\Column(name="metaTitle", type="text", nullable=true)
     */
    protected $metaTitle;
    /**
     * @var text $metaDescription
     * 
     * @ORM\Column(name="metaDescription", type="text", nullable=true)
     */
    
    protected $metaDescription;
    /**
     * @var text $metaKeyword
     * 
     * @ORM\Column(name="metaKeyword", type="text", nullable=true)
     */    
    protected $metaKeyword;
    
    protected $fields = array('content', 'title', 'translit', 'metaTitle', 'metaDescription', 'metaKeyword');
    
    /**
     * Set description
     *
     * @param string $description
     * @return MenuSys
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set title
     *
     * @param string $title
     * @return Menu
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
     * Set description
     *
     * @param string $content
     * @return Menu
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Set kod
     *
     * @param integer $kod
     * @return Menu
     */
    public function setKod($kod)
    {
        $this->kod = $kod;
    
        return $this;
    }

    /**
     * Get kod
     *
     * @return integer 
     */
    public function getKod()
    {
        return $this->kod;
    }
    /**
     * Set translit
     *
     * @param string $translit
     * @return Menu
     */
    public function setTranslit($translit)
    {
        $this->translit = $translit;
    
        return $this;
    }

    /**
     * Get translit
     *
     * @return string 
     */
    public function getTranslit()
    {
        return $this->translit;
    }
    
    /**
     * Set icon
     *
     * @param string $icon
     * @return Menu
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    
        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }
    /**
     * Set smallIcon
     *
     * @param string $icon
     * @return Menu
     */
    public function setSmallIcon($icon)
    {
        $this->smallIcon = $icon;
    
        return $this;
    }

    /**
     * Get smallIcon
     *
     * @return string 
     */
    public function getSmallIcon()
    {
        return $this->smallIcon;
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
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }
    
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }    
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }
    
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }    
    public function setMetaKeyword($metaKeyword)
    {
        $this->metaKeyword = $metaKeyword;
    }
    
    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }    
    
}