<?php

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Command;

require_once(realpath(dirname(__FILE__)) . '/../../../../../../../vendor/kertz/twitteroauth/twitteroauth/twitteroauth.php');

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CollectCommand extends ContainerAwareCommand
{
    
    protected function configure()
    {   
        $this->setName('bestbadtweets:collect')
             ->setDescription('Collects twitter favorites')
             ->setHelp(<<<EOT
The <info>{$this->getName()}</info> Runs the Collector::collect() method.
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('bestbadtweets.collector')->collect();
    }

}
