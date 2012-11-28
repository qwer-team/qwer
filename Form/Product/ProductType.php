<?php

namespace Itc\AdminBundle\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Itc\AdminBundle\Tools\LanguageHelper;
use Doctrine\ORM\EntityRepository;

class ProductType extends AbstractType
{
    private $id;
    
    public function __construct( $em, $id = NULL, $update = false )
    {
        $this->id = $id ;
        $this->update = $update;
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $qb = $this->em->getRepository( 'ItcAdminBundle:Product\Product' )
                                            ->createQueryBuilder( 'P' );
        
        if( $options["attr"]["new"] && ! $this->update ){
            $qb->select("P")->where("P.id is null");
        }
        if( !$this->update && ! $options["attr"]["new"])
            $qb = $qb->select( "P" )->Join( "P.products",  "T")
                                ->where( "T.id = {$this->id}" );
        $builder
            ->add('kod')
            ->add('price')
            ->add('brand', null, array('required'=>NULL))
            ->add('article', null, array('required'=>NULL))
            ->add('warranty', null, array('required'=>NULL))
            ->add('topSales', null, array('required'=>NULL))
            ->add('novelty', null, array('required'=>NULL))
            ->add('bestSeller', null, array('required'=>NULL))
            ->add('count', null, array('required'=>NULL))
            ->add('unit', null, array('required'=>NULL))
            ->add('productGroup', null, array('required'=>NULL))
            //*
              ->add('relations', NULL, array(
                'required'=>NULL,
               //*
                 'query_builder' => $qb,
                //*/
                'attr' => array(
                    'class' => 'relationsProduct',
                    'style' => 'width: 75%;',
                    'multiple' => 'multiple'
                )
            ))//*/

            ->add("smallIcon");
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
            'data_class' => 'Itc\AdminBundle\Entity\Product\Product'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_product_producttype';
    }
}
