<?php

namespace Itc\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Itc\AdminBundle\Entity\MenuSys;

class MenuSysImageType extends AbstractType 
{
    //put your code here
    public function getName() {
        return 'itc_adminbundle_menusysimagetype';
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
            'data_class' => 'Itc\AdminBundle\Entity\MenuSys\MenuSys'
        ));
    }
}

?>
