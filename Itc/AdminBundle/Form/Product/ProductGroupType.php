<?php

namespace Itc\AdminBundle\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Itc\AdminBundle\Tools\LanguageHelper;

class ProductGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('parent_id')
//            ->add('icon')
//            ->add('kod')
            ->add('parent', null, array("label" => "parent"))
            ->add('smallIcon')
        ;
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
                    ->add( $lang.'Translation.metaKeyword', null,
                                        array("label" => "MetaKeyword", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.metaDescription', null,
                                        array("label" => "MetaDescription", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.metaTitle', null,
                                        array("label" => "MetaTitle", 
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
            'data_class' => 'Itc\AdminBundle\Entity\Product\ProductGroup'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_product_productgrouptype';
    }
}
