<?php

namespace Itc\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Itc\AdminBundle\Tools\LanguageHelper;


class MenuType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag')
            ->add('routing')
            ->add('kod', null, array('required'=>NULL) )
            ->add('parent_id')
            ->add('visible', null, array('required'=>NULL))
            ->add('keywords', null, array(
                'attr' => array(
                    'class' => "select2",
                    'multiple' => "multiple",
                    'style' =>'width: 75%;'
                ),
                "required" => null
                ))
        ;
        if($options["attr"]["new"]){
            $builder->add( 'iconImage', 'file', array('required'=>NULL) );
        }
        $tinymce = array( 
            'attr' => array('class' => 'tinymce', 'data-theme' => 'medium', ) );
        
        $languages = $this->getLanguages();     
         
        foreach($languages as $k => $lang ){
            
            $builder->add( $lang.'Translation.title', null, 
                                        array("label" => "Title", 
                                             'required'=>NULL))
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
                          array_merge($tinymce, 
                          array("label" => "Content", 'required'=>NULL)) );
                        ;
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
            'data_class' => 'Itc\AdminBundle\Entity\Menu\Menu'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_menutype';
    }
}
