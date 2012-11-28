<?php

namespace Itc\AdminBundle\Form\Gallery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Itc\AdminBundle\Tools\LanguageHelper;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options["attr"]["new"]){
            $builder->add('image')
                    ->add('tag', null, 
                            array("label" => "Tag", 'required'=>NULL))
                    ->add('gallery', null, array('attr' => array('class' => 'hidden')))
                ;
        }
        else
        {
            $builder->add('tag')
                ->add('gallery')
            ;
            $tinymce = array( 
                'attr' => array('class' => 'tinymce', 'data-theme' => 'medium', ) );

            $languages = $this->getLanguages();

            foreach( $languages as $k => $lang ){
                $builder->add( $lang.'Translation.title', null,  
                                array("label" => "Title") )
                    ->add( $lang.'Translation.description', 'textarea', 
                            array_merge($tinymce, array("label" => "description")));
            }
        }
    }
    private function getLanguages()
    {
        $locale = LanguageHelper::getLocale();
        $lngs = LanguageHelper::getLanguages();
        /* @var $languages type */
        $languages = !\is_null($lngs)? $lngs: array($locale);
        return $languages;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\Gallery\Image'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_gallery_imagetype';
    }
}
