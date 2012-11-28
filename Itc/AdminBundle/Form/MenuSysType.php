<?php

namespace Itc\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuSysType extends AbstractType
{
    private $locale;
    public function __construct($locale, $languages = NULL )
    {
        $this->languages = ! is_null( $languages ) ? $languages : array( $locale ) ;
        $this->locale = $locale;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disable = array( 'disabled'=>'disabled' );
        $builder
            ->add('tag')
            ->add('kod', null, $disable )
            ->add('parent_id')
            ->add("routing")
            ->add('visible', null, array('required'=>NULL))
            //->add('parent',  "entity", array('required'=>NULL, "class" => "Itc\AdminBundle\Entity\MenuSys", "property" => $this->locale."Translation.title"))
        ;

        $tinymce = array('class' => 'tinymce', 'data-theme' => 'medium' );
        
        foreach( $this->languages as $k => $lang ){
            
            $builder->add( $lang.'Translation.title', null, 
                                array("label" => "Title") )
                    ->add( $lang.'Translation.translit', null, 
                                array("label" => "Translit",
                                        'disabled'=>'disabled') )
                    ->add( $lang.'Translation.metaKeyword', null,
                                        array("label" => "MetaKeyword", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.metaDescription', null,
                                        array("label" => "MetaDescription", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.metaTitle', null,
                                        array("label" => "MetaTitle", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.description', 'textarea',
                                array("label" => "Description"))
                    ->add( $lang.'Translation.content', 'textarea', 
                                array("label" => "Content",
                                    'attr' => $tinymce));
                    
        }
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\MenuSys\MenuSys'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_menusystype';
    }
}
