<?php

namespace Itc\AdminBundle\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Itc\AdminBundle\Tools\LanguageHelper;


class BrandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options["attr"]["new"]){
            $builder->add( 'iconImage', 'file', array('required'=>NULL) );
        }
        $languages = $this->getLanguages();     

        foreach($languages as $k => $lang ){
            
            $builder->add( $lang.'Translation.title', null,
                                        array("label" => "Title"))
                    ->add( $lang.'Translation.translit', null,
                                        array("label" => "Translit", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.description', null,
                                        array("label" => "Description", 
                                             'required'=>NULL))
                    ->add( $lang.'Translation.content', 'textarea',                           
                          array("label" => "Content", 'required'=>NULL, 
                          'attr' => array('class' => 'tinymce', 'data-theme' => 'medium' ) )
                    );
        }
        
    }

    private function getLanguages()
    {
        $locale = LanguageHelper::getLocale();
        $lngs = LanguageHelper::getLanguages();
        $languages = !\is_null($lngs)? $lngs: array($locale);
        return $languages;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\Product\Brand'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_product_brandtype';
    }
}
