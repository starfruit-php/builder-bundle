<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Knp\Component\Pager\PaginatorInterface;

class ListingExtension extends AbstractExtension
{
    protected $paginator;

    public function __construct(
        PaginatorInterface $paginator
    )
    {
        $this->paginator = $paginator;
    }

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

        $keyParams = ['orderKey', 'order', 'unpublished'];
        foreach ($keyParams as $keyParam) {
            $paramValue = isset($params[$keyParam]) ? $params[$keyParam] : null;
            if ($paramValue) {
                $list->{'set' . ucfirst($keyParam)}($paramValue);
            }
        }

        if (isset($params['orderRandom']) && $params['orderRandom']) {
            $list->setOrderKey("RAND()", false);
        }

        $page = isset($params['page']) ? $params['page'] : 0;
        $limit = isset($params['limit']) ? $params['limit'] : 0;
        if (!$page && $limit) {
            $list->setLimit($limit);
        }

        if ($page && $limit) {
            $pagination = $this->paginator->paginate(
                $list,
                $page,
                $limit,
            );

            return compact('list', 'pagination');
        }

        return $list;
    }
}
