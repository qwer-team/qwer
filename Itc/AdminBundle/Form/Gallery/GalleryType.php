<?php

namespace Itc\AdminBundle\Form\Gallery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array("label" => "title"))
            ->add('menuId','hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\Gallery\Gallery'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_gallery_gallerytype';
    }
}
