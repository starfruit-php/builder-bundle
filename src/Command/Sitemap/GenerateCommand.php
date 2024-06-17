<?php

namespace Starfruit\BuilderBundle\Command\Sitemap;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

use Starfruit\BuilderBundle\Tool\SystemTool;
use Starfruit\BuilderBundle\Sitemaps\Command;

class GenerateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('builder:sitemap:generate')
            ->setDescription('Generate sitemap xml files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeInfo("Running");
        $domain = SystemTool::getDomain();

        if (!$domain) {
            $this->writeError("Main domain not found. Check builder:option-setting to setup!");
            return AbstractCommand::INVALID;
        }
        
        try {
            Command::dumpSitemap($domain);

            $this->writeInfo("Check folder public to get sitemap data");
        } catch (\Throwable $e) {
            $this->writeError("Something wrong!");
        }

        return AbstractCommand::SUCCESS;
    }
}
