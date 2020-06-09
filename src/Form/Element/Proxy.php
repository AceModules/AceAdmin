<?php

namespace AceAdmin\Form\Element;

use DoctrineModule\Form\Element\Proxy as DoctrineProxy;

class Proxy extends DoctrineProxy
{
    /**
     * @param array $option_attributes
     */
    public function setOptionAttributes(array $option_attributes) : void
    {
        foreach ($option_attributes as $key => $value) {
            if (method_exists($this->getTargetClass(), $value)) {
                $option_attributes[$key] = function($entity) use ($value) {
                    return $entity->$value();
                };
            }
        }

        parent::setOptionAttributes($option_attributes);
    }

}