<?php

namespace Itc\AdminBundle\Entity\Gallery;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Itc\AdminBundle\Entity\TranslatableEntity;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Itc\AdminBundle\ItcAdminBundle;
/**
 * Itc\AdminBundle\Entity\Image
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Image extends TranslatableEntity
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
     * @var string $src
     *
     * @ORM\Column(name="src", type="string", length=255)
     */
    private $src;
    /**
     * @var string $smallSrc
     *
     * @ORM\Column(name="smallSrc", type="string", length=255, nullable=true)
     */
    private $smallSrc;

    /**
     * @var string $tag
     *
     * @ORM\Column(name="tag", type="string", length=255)
     */
    private $tag;
    /**
     * 
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",
     * onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="images")
     * 
     */
    private $gallery;
    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;
    /**
     * @var string $title
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    /**
     * @Assert\File(
     *     maxSize="2M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="image", fileNameProperty="src")
     *
     * @var File $iconImage
     */
    private $image;
    /**
     * @Assert\File(
     *     maxSize="2M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="image", fileNameProperty="smallSrc")
     */
    private $smallImage;
    /**
    * @ORM\OneToMany(
    *     targetEntity="ImageTranslation",
    *     mappedBy="translatable",
    *     cascade={"persist"}
    * )
    */
    protected $translations;
    protected $fields = array( 'title', 'description');
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
     * Set src
     *
     * @param string $src
     * @return Image
     */
    public function setSrc($src)
    {
        $this->src = $src;
    
        return $this;
    }

    /**
     * Get src
     *
     * @return string 
     */
    public function getSrc()
    {
        return $this->src;
    }
    /**
     * Set smallSrc
     *
     * @param string $smallSrc
     * @return Image
     */
    public function setSmallSrc($smallSrc)
    {
        $this->smallSrc = $smallSrc;
    
        return $this;
    }

    /**
     * Get smallSrc
     *
     * @return string 
     */
    public function getSmallSrc()
    {
        return $this->smallSrc;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Image
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Image
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
     * @param string $description
     * @return Image
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
     * Set menu
     *
     * @param Itc\AdminBundle\Entity\Gallery $menu
     * @return Image
     */
    public function setGallery(\Itc\AdminBundle\Entity\Gallery\Gallery
                                                            $gallery = null)
    {
        $this->gallery = $gallery;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return Itc\AdminBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }
    
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
    
    public function getImage()
    {
        return $this->image;
    }
    public function setSmallImage($smallImage)
    {
        $this->smallImage = $smallImage;
        return $this;
    }
    
    public function getSmallImage()
    {
        return $this->smallImage;
    }
    /**
    * @ORM\PostPersist
    */
    public function createSmallImage()
    {
        $imagine = new Imagine();
        $mode    = ImageInterface::THUMBNAIL_OUTBOUND;
        $size    = new Box(210, 210);
       
        $container = ItcAdminBundle::getContainer();
        $helper = 
            $container->get('vich_uploader.templating.helper.uploader_helper');
        $rootDir =  $container->get('kernel')->getRootDir();
        $relPath = $helper->asset($this, 'image');
        $pathParts = explode("/", $relPath);
        $folder = "";
        for($i = 0; $i < count($pathParts) - 1; $i++)
        {
            $folder .= $pathParts[$i]."/";
        }
        $pathToImage = $rootDir. "/../web".
                       $helper->asset($this, 'image');
        $this->smallSrc = "small_{$this->src}";
        $pathToSmall =$rootDir."/../web".
                      $folder.$this->smallSrc;
        $image = $imagine->open($pathToImage);
        if($image->getSize()->getWidth() > 100 || 
                $image->getSize()->getHeight() > 100){
            $this->smallImage = 
                    $image->thumbnail($size, $mode)->save($pathToSmall);
        }
        else
        {
            $this->smallImage = $image->save($pathToSmall);
        }
    }
    
    /**
     * @ORM\PostPersist
     */
    public function thumbImage()
    {
        $imagine = new Imagine();
        $mode    = ImageInterface::THUMBNAIL_INSET;
        $size    = new Box(1024, 800);
        
        $container = ItcAdminBundle::getContainer();
        $helper = 
            $container->get('vich_uploader.templating.helper.uploader_helper');
        $rootDir =  $container->get('kernel')->getRootDir();
        $pathToImage = $rootDir. "/../web".$helper->asset($this, 'image');
        $image = $imagine->open($pathToImage);
        if($image->getSize()->getWidth() > 1024 || $image->getSize()->getHeight() > 800){
            $image->thumbnail($size, $mode)
                  ->save($pathToImage);
        }
    }
    
}