<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Command\CollectDaemon;

require_once(realpath(dirname(__FILE__)) . '/../../../../../../vendor/twitteroauth/twitteroauth/twitteroauth.php');

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use CodeMeme\Bundle\CodeMemeDaemonBundle\Daemon;

class CollectDaemonStartCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {   
        $this->setName('bestbadtweets:collect:start')
             ->setDescription('Starts the bestbadtweets collect daemon')
             ->setHelp(<<<EOT
The <info>{$this->getName()}</info> Run the bestbadtweets collect daemon in the background.
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $daemon = new Daemon($this->getContainer()->getParameter('collect.daemon.options'));
        $daemon->setInterval(300); //twitter rate limits to 350 requests per hour
        $daemon->start();
        
        while ($daemon->isRunning()) {
            $this->getContainer()->get('bestbadtweets.collector')->collect();
        }
        
        $daemon->stop();
    }

}
