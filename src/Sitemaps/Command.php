<?php

namespace Starfruit\BuilderBundle\Sitemaps;

use Symfony\Component\Process\Process;

class Command
{
    public static function dumpSitemap($domain, $section = null)
    {
        $command = 'presta:sitemaps:dump --base-url=' . $domain;

        if ($section) {
            $command .= ' --section=' . $section;
        }

        $process = new Process(explode(' ', 'php ' . str_replace("\\", '/', PIMCORE_PROJECT_ROOT) . '/bin/console ' . $command), null, null, null, 900);

        $process->run();
    }
}
