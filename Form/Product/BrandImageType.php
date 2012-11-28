<?php

namespace Itc\AdminBundle\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Itc\AdminBundle\Entity\Product\Brand;

class BrandImageType extends AbstractType 
{
    public function getName() {
        return 'itc_adminbundle_brandimagetype';
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('iconImage', 'file', 
                        array('label' => 'icon',
                                'data_class' => 
                                    'Symfony\Component\HttpFoundation\File\File'
                        )
                    )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\Product\Brand'
        ));
    }

}

?>
