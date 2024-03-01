<?php

namespace Starfruit\BuilderBundle\Tool;

class ParameterTool
{
    public static function getLinkGenerateObjects()
    {
        return \Pimcore::getContainer()->getParameter('starfruit_builder.link_generate_objects');
    }
}
