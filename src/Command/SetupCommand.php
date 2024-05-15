<?php

namespace Starfruit\BuilderBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Starfruit\BuilderBundle\Service\DatabaseService;

class SetupCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('builder:setup')
            ->setDescription('Setup Starfruit Builder configs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DatabaseService::createBuilderSeo();

        return 1;
    }
}
