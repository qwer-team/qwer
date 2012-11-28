<?
namespace Itc\AdminBundle\Entity\MenuSys;
use \Gedmo\Translator\TranslationProxy;
use Symfony\Component\Validator\Constraints as Assert;

class MenuSysProxy extends TranslationProxy
{
    
    public function setTitle($name)
    {
        return $this->setTranslatedValue('title', $name);
    }
    /**
     * @Assert\MinLength(10)
     */
    public function getTitle()
    {
         $title= $this->getTranslatedValue('title');
         if(null === $title)
         {
             $title = "";
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
    public function setTranslit($translit)
    {
        return $this->setTranslatedValue('translit', $translit);
    }
    /**
     * @Assert\MinLength(10)
     */
    public function getTranslit()
    {
         $translit= $this->getTranslatedValue('translit');
         return $translit;
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
