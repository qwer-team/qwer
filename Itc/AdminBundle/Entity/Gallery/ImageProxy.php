<?
namespace Itc\AdminBundle\Entity\Gallery;
use \Gedmo\Translator\TranslationProxy;
use Symfony\Component\Validator\Constraints as Assert;

class ImageProxy extends TranslationProxy
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
             $title = "";
         }
         return $title;
    }
    public function setDescription($name)
    {
        return $this->setTranslatedValue('description', $name);
    }
    
    public function getDescription()
    {
         $title= $this->getTranslatedValue('description');
         return $title;
    }
}
