<?php

namespace AceAdmin\Form\Element;

use DoctrineModule\Form\Element\ObjectSelect;

class ObjectLiveSearch extends ObjectSelect
{
    /**
     * @var array
     */
    protected $attributes = array(
        'type' => 'select',
        'class' => 'selectpicker',
        'data-live-search' => true,
        'data-min-length' => 3,
        'data-selected-text-format' =>'count > 3',
    );

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
     * @param  string|null $emptyOption
     * @return $this
     */
    public function setEmptyOption($emptyOption)
    {
        $this->attributes['data-title'] = $emptyOption;
        return $this;
    }
}