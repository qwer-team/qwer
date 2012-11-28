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
/**
 * Description of FillSchemaCommand
 *
 * @author root
 */
class FillSchemaCommand extends Command 
{
    protected function configure() 
    {
        parent::configure();

        $this
                ->setName('iab:schema:fill')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = \Itc\AdminBundle\ItcAdminBundle::getContainer();
        $param = $container->getParameter("schema_fill_file");
        $path = __DIR__."/../Resources/".$param;
        $output->write($path."\n");
        $file = new File($path);
        $reader = $file->openFile();
        $sql = "";
        $em = $container->get("doctrine")->getManager();
        while(!$reader->eof())
        {
            $sql .= $reader->fgets();
        }
        $query = $em->getConnection()->prepare($sql);
        $query->execute();
        $output->write("Completed!\n");
    }
}