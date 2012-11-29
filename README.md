itcadmin
========

AppKernel.php
<code>
    
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
</code>

config.yml
<code>

    import:
        - { resource: '@ItcAdminBundle/Resources/config/config.yml' }
</code>

routing.yml
<code>

    _itc:
      resource: '@ItcAdminBundle/Resources/config/routing.yml'
</code>

security.yml
<code>

    jms_security_extra:
        secure_all_services: false
        expressions: true
        
    security:
        providers:
            chain_provider:
                chain:
                    providers: [in_memory, fos_userbundle]
                    
            fos_userbundle:
                id: fos_user.user_provider.username
                
            in_memory:
                memory:
                    users:
                        qwer: { password: admin, roles: 'ROLE_ADMIN' }
        encoders:
            FOS\UserBundle\Model\UserInterface: sha512
            Symfony\Component\Security\Core\User\User: plaintext
    
        firewalls:
            main:
                pattern: ^/
                form_login:
                    provider: chain_provider
                    csrf_provider: form.csrf_provider
                    always_use_default_target_path: false
                    default_target_path: /itc
                    target_path_parameter: _target_path
                logout:
                    path:   /logout
                    target: /itc
                
                anonymous:    true
            
                
        access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/itc/, role: ROLE_USER }
    
        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: ROLE_ADMIN
</code>
<code>
chmod -R 777 vendor/qwerteam/
</code>
