<?
namespace Itc\AdminBundle\Entity\Menu;
use Itc\AdminBundle\Entity\TranslationProxy;
use Symfony\Component\Validator\Constraints as Assert;

class MenuProxy extends TranslationProxy
{
    
    public function setTitle($name)
    {
        return $this->setTranslatedValue('title', $name);
    }
    
    public function getTitle()
    {
         $title= $this->getTranslatedValue('title');
         if(null === $title)
         {
             $title = $this->title;
         }
         return $title;
    }
    public function setDescription($description)
    {
        return $this->setTranslatedValue('description', $description);
    }
    public function getDescription()
    {
         $description= $this->getTranslatedValue('description');
         if(null === $description)
         {
             $description = "";
         }
         return $description;
    }
    public function setContent($content)
    {
        return $this->setTranslatedValue('description', $content);
    }
    public function getContent()
    {
         $content= $this->getTranslatedValue('description');
         if(null === $content)
         {
             $content = "";
         }
         return $content;
    }
    
    public function setTranslit($name)
    {
        return $this->setTranslatedValue('translit', $name);
    }
    /**
     * @Assert\MinLength(10)
     */
    public function getTranslit()
    {
         $title= $this->getTranslatedValue('translit');
         return $title;
    }
    public function setParent($name)
    {
        return $this->setTranslatedValue('parent', $name);
    }
    /**
     * @Assert\MinLength(10)
     */
    public function getParent()
    {
        return $this->getTranslatedValue('parent');
    }
}
