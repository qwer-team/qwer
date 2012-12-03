<?php

namespace Itc\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Itc\AdminBundle\Tools\StringOutput;
use Symfony\Component\Process\Process;

/**
 * @Route("/commands")
 * @Template()
 */
class CommandsController extends Controller {

    /**
     * @Route("/", name="commands")
     * @Template()
     */
    public function indexAction() {
        return array();
    }
    
    /**
     * @Route("/cache_clear", name="cache_clear") 
     */
    public function cacheClearAction()
    {
        $processString = "php app/console cache:clear --env=prod; ".
                         "chmod -R 777 app/cache";
        $this->executeProcess($processString);
        return $this->redirect($this->generateUrl("commands"));
    }
    
    /**
     * @Route("/datatbase_create", name="datatbase_create") 
     */
    public function datatbaseCreateAction()
    {
        $processString = "php app/console doctrine:database:create";
        $this->executeProcess($processString);
        return $this->redirect($this->generateUrl("commands"));
    }

    /**
     * @Route("/schema_update", name="schema_update") 
     */
    public function schemaUpdateAction()
    {
        $processString = "php app/console doctrine:schema:update --force";
        $this->executeProcess($processString);
        return $this->redirect($this->generateUrl("commands"));
    }

    /**
     * @Route("/schema_fill", name="schema_fill") 
     */
    public function schemaFillAction()
    {
        $processString = "php app/console iab:schema:fill";
        $this->executeProcess($processString);
        return $this->redirect($this->generateUrl("commands"));
    }
    
    private function executeProcess($processString)
    {
        $path = $this->get('kernel')->getRootDir();
        $process = new Process("cd ".$path."/../; ".$processString);
        $process->run();
        $this->get('session')->setFlash(
            'notice',
            $process->getOutput(). $process->getErrorOutput()
        );
    }
}