<?php

namespace Main\SiteBundle\Service;

use Swift_Mailer;
use Itc\AdminBundle\ItcAdminBundle;

class SendMailService
{
    private $container;
    private $from;
    private $to;
    private $body;
    private $subject;

    public function __construct()
    {
        $this->container = ItcAdminBundle::getContainer(); 
    }

    public function from($from)
    {
        $this->from = $from;
        
        return $this;
    }
    
    public function to($to)
    {
        $this->to = $to;
        
        return $this;
    }

    public function body($body)
    {
        $this->body = $body;
        
        return $this;
    }
    
    public function subject($subject)
    {
        $this->subject = $subject;
        
        return $this;
    }
    
    public function send()
    {
        $from    = $this->from;
        $to      = $this->to;
        $body    = $this->body;
        $subject = $this->subject;

        $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($from)
                    ->setTo($to)
                    ->setBody($body, 'text/html');

        $this->container->get('mailer')->send($message);
    }
    
    public function sendMessage($from, $to, $body)
    {
        $this->from($from);
        $this->to($to);
        $this->body($body);
        $this->send();
    }
}

?>
