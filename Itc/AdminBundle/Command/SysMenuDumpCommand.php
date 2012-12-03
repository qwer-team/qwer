<?php

namespace Itc\AdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\HttpFoundation\File\File;

class SysMenuDumpCommand extends Command 
{
    private $container;
    
    protected function configure() 
    {
        parent::configure();

        $this
                ->setName('iab:schema:dump')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = \Itc\AdminBundle\ItcAdminBundle::getContainer();
        $em = $this->container->get("doctrine")->getManager();
        
        $repo = $em->getRepository("ItcAdminBundle:MenuSys\MenuSys");
        $qb = $repo->createQueryBuilder('M')
                         ->select('M')
                         ->where('M.system_id is not NULL');
        
        $entities = $qb->getQuery()->execute();
        
        $repo = $em->getRepository("ItcAdminBundle:MenuSys\MenuSysTranslation");
        $qb = $repo->createQueryBuilder('T')
                         ->select('T')
                         ->innerJoin("T.translatable", "M")
                         ->where('M.system_id is not NULL');
        
        $translations = $qb->getQuery()->execute();
        
        $menuSql = $this->createMenuSql($entities);
        
        $translatinSql = $this->getTranslationsSql($translations);
        
        $this->flushDump($menuSql.$translatinSql);
        
        $output->write("Completed!\n");
    }
    
    /**
     *
     * @param array $entities 
     */
    private function createMenuSql($entities)
    {
        $sql = "INSERT INTO `MenuSys` (`system_id`, `parent_id`, `tag`, ".
               " `visible`, `routing`, `title`, `translit`, `kod`, `content`, ".
               "`description`, `metaTitle`, `metaDescription`, `metaKeyword`) ".
               "VALUES\n";
        
        $entitiesSql = array();
        foreach($entities as $entity)
        {
            $sqlEnt = "(";
            $attrs = array();
            
            $attrs[] = $entity->getSystemId();
            $parentId = $entity->getParentId();
            $attrs[] = is_null($parentId) ? "NULL": $parentId;
            $attrs[] = "'".$entity->getTag()."'";
            $attrs[] = (int)$entity->getVisible();
            $attrs[] = "'".$entity->getRouting()."'";
            $attrs[] = "'".$entity->getTitle()."'";
            $attrs[] = "'".$entity->getTranslit()."'";
            $attrs[] = $entity->getKod();
            $attrs[] = "'".$entity->getContent()."'";
            $attrs[] = "'".$entity->getDescription()."'";
            $attrs[] = "'".$entity->getMetaTitle()."'";
            $attrs[] = "'".$entity->getMetaDescription()."'";
            $attrs[] = "'".$entity->getMetaKeyword()."'";
            
            $sqlEnt .= implode(",", $attrs);
            
            $sqlEnt .= ")";
            
            $entitiesSql[] = $sqlEnt;
        }
        
        $sql .= implode(",\n", $entitiesSql);
        
        $sql .= "ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                tag = VALUES(tag),
                routing = VALUES(routing),
                translit = VALUES(translit),
                content = VALUES(content),
                description = VALUES(description),
                metaTitle = VALUES(metaTitle),
                metaDescription = VALUES(metaDescription);\n";
        
        return $sql;
    }
    
    private function getTranslationsSql($translations)
    {
        $sql = "INSERT INTO `MenuSysTranslation` (`translatable_id`, ".
               "`locale`,  `property`,  `value`) ".
               "VALUES\n";
        
        $entitiesSql = array();
        foreach($translations as $translation)
        {
            $sqlEnt = "(";
            $attrs = array();
            
            $systemId = $translation->getTranslatable()->getSystemId();
            $attrs[] = "(SELECT id FROM MenuSys WHERE system_id = $systemId)";
            $attrs[] = "'".$translation->getLocale()."'";
            $attrs[] = "'".$translation->getProperty()."'";
            $attrs[] = "'".$translation->getValue()."'";
            
            $sqlEnt .= implode(",", $attrs);
            
            $sqlEnt .= ")";
            
            $entitiesSql[] = $sqlEnt;
        }
        
        $sql .= implode(",\n", $entitiesSql);
        $sql .= "ON DUPLICATE KEY UPDATE
            value = VALUES(value);\n";

        return $sql;
    }
    
    private function flushDump($data)
    {
        $dumpName = $this->container->getParameter("schema_dump_file");
        
        $kernel = $this->container->get("kernel");
        $path       = 
            $kernel->locateResource("@ItcAdminBundle/Resources/".$dumpName);
        
        file_put_contents($path, $data);
    }
}