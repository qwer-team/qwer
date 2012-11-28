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

class TestCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
                ->setName('iab:test')
                ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
         // your code here
         $dialog = $this->getDialogHelper();
         $entity = Validators::validateEntityName($input->getOption('entity'));
         list($bundle, $entity) = $this->parseShortcutNotation($entity);
         $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);
    }
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }
}
