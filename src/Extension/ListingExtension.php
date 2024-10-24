<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ListingExtension extends AbstractExtension
{
    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_listing_object', [$this, 'listing']),
        ];
    }

    public function listing($className, $params = [])
    {
        $className = '\\Pimcore\\Model\\DataObject\\' . $className;
        $list = call_user_func_array($className . '::getList', []);

        $condStr = isset($params['condition']['string']) ? $params['condition']['string'] : null;
        if ($condStr) {
            $condArr = isset($params['condition']['array']) ? $params['condition']['array'] : [];
            $list->setCondition($condStr, $condArr);
        }

        $keyParams = ['limit', 'orderKey', 'order', 'unpublished'];
        foreach ($keyParams as $keyParam) {
            $paramValue = isset($params[$keyParam]) ? $params[$keyParam] : null;
            if ($paramValue) {
                $list->{'set' . ucfirst($keyParam)}($paramValue);
            }
        }

        return $list;
    }
}
