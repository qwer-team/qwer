<?php

namespace Itc\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Itc\AdminBundle\Entity\MenuSys;

class MenuSysNewType extends AbstractType
{
    private $locale;
    private $languages;

    public function __construct($locale, $languages = NULL )
    {
        $this->languages = ! is_null( $languages ) ? $languages : array( $locale ) ;
        $this->locale = $locale;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disable = array( 'disabled'=>'disabled' );
        $builder
            ->add( 'tag')
            ->add( 'kod', null, $disable )
            ->add( 'parent_id')
            ->add( "routing")
            ->add( 'visible', null, array('required'=>NULL))
            ->add( 'iconImage', 'file', array('required'=>NULL) )
            //->add('parent',  "entity", array('required'=>NULL, "class" => "Itc\AdminBundle\Entity\MenuSys", "property" => $this->locale."Translation.title"))
        ;

        $tinymce = array( 
            'attr' => array('class' => 'tinymce', 'data-theme' => 'medium', ) );
        
        foreach( $this->languages as $k => $lang ){
            
            $builder->add( $lang.'Translation.title', null, array("label" => "Title") )
                    ->add( $lang.'Translation.translit', null, 
                          array_merge($disable, array("label" => "Translit")) )
                    ->add( $lang.'Translation.metaKeyword', null,
                                        array("label" => "MetaKeyword", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.metaDescription', null,
                                        array("label" => "MetaDescription", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.metaTitle', null,
                                        array("label" => "MetaTitle", 
                                              'required'=>NULL))
                    ->add( $lang.'Translation.description','textarea',
                                            array("label" => "Description"))
                    ->add( $lang.'Translation.content', 'textarea', 
                            array_merge($tinymce, array("label" => "Content") ));
                    
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
        return 'itc_adminbundle_menusysnewtype';
    }
}
