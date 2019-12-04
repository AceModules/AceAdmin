<?php

namespace AceAdmin\Form\Element;

use DoctrineModule\Form\Element\ObjectSelect;
use Zend\Router\RouteStackInterface;

class ObjectLiveSearch extends ObjectSelect
{
    /**
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $attributes = [
        'type' => 'select',
        'class' => 'selectpicker',
        'data-live-search' => true,
        'data-min-length' => 3,
        'data-selected-text-format' => 'count > 3',
    ];

    /**
     * @var bool
     */
    protected $disableInArrayValidator = true;

    /**
     * @return Proxy
     */
    public function getProxy()
    {
        if (null === $this->proxy) {
            $this->proxy = new Proxy();
        }
        return $this->proxy;
    }

    /**
     * @param RouteStackInterface $router
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return RouteStackInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($this->options['ajax_route'])) {
            $this->setAjaxRoute($this->options['ajax_route']);
        }

        return $this;
    }

    /**
     * @param  string|null $emptyOption
     * @return $this
     */
    public function setEmptyOption($emptyOption)
    {
        $this->attributes['data-title'] = $emptyOption;
        return $this;
    }

    /**
     * @param array $ajaxRoute
     * @return $this
     */
    public function setAjaxRoute($ajaxRoute)
    {
        if (isset($ajaxRoute['params']['entity'])) {
            $ajaxRoute['params']['entity'] = strtolower(str_replace(' ', '-', $ajaxRoute['params']['entity']));
        }

        $this->attributes['data-ajax-url'] = $this->router->assemble($ajaxRoute['params'], $ajaxRoute);

        return $this;
    }
}
