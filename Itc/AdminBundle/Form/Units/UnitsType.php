<?php

namespace Itc\AdminBundle\Form\Units;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Itc\AdminBundle\Tools\LanguageHelper;

class UnitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $this->getLanguages();     

        foreach($languages as $k => $lang ){
            
            $builder->add( $lang.'Translation.title', null,
                                        array("label" => "Title"));
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
            'data_class' => 'Itc\AdminBundle\Entity\Units\Units'
        ));
    }

    public function getName()
    {
        return 'itc_adminbundle_units_unitstype';
    }
}
