<?php

namespace Starfruit\BuilderBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Starfruit\BuilderBundle\Model\Option;

class OptionSettingCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('builder:option-setting')
            ->setDescription('Setup required options for sitemap tool')
            ->addOption(
                // this is the name that users must type to pass this option (e.g. --iterations=5)
                'main-domain',
                // this is the optional shortcut of the option name, which usually is just a letter
                // (e.g. `i`, so users pass it as `-i`); use it for commonly used options
                // or options with long names
                null,
                // this is the type of option (e.g. requires a value, can be passed more than once, etc.)
                InputOption::VALUE_REQUIRED,
                // the option description displayed when showing the command help
                'https://example.com'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeInfo("Running...");
        $mainDomain = $input->getOption('main-domain');

        if (empty($mainDomain)) {
            $this->writeError("Give a value for option --main-domain!");
            return AbstractCommand::INVALID;
        }

        $option = Option::getOrCreate(Option::MAIN_DOMAIN_NAME);
        $option->setContent($mainDomain);
        $option->save();

        $this->writeInfo("Set main domain");

        return AbstractCommand::SUCCESS;
    }
}
