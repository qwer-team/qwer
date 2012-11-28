itcadmin
========

AppKernel.php

new Itc\AdminBundle\ItcAdminBundle(),
new FOS\UserBundle\FOSUserBundle(),
new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),
new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
new Vich\UploaderBundle\VichUploaderBundle(),
new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
new Genemu\Bundle\FormBundle\GenemuFormBundle()


config.php
import:
    - { resource: '@ItcAdminBundle/Resources/config/config.yml' }

routing.php
_itc:
  resource: '@ItcAdminBundle/Resources/config/routing.yml'

chmod -R 777 vendor/qwerteam/

