<?php

namespace AceAdmin\Form\Element;

use Doctrine\ORM\Mapping;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AbstractAnnotationsListener;
use Zend\Stdlib\ArrayObject;

class ObjectElementListener extends AbstractAnnotationsListener
{
    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('configureElement', [$this, 'handleMappingAnnotation']);
    }

    /**
     * @param EventInterface $e
     */
    public function handleMappingAnnotation(EventInterface $e)
    {
        $annotation = $e->getParam('annotation');
        if ((!$annotation instanceof Mapping\OneToOne) &&
            (!$annotation instanceof Mapping\OneToMany) &&
            (!$annotation instanceof Mapping\ManyToOne) &&
            (!$annotation instanceof Mapping\ManyToMany)) {
            return;
        }

        $elementSpec = $e->getParam('elementSpec');
        $options = array('target_class' => $annotation->targetEntity);

        if (isset($elementSpec['spec']['options'])) {
            if (is_array($elementSpec['spec']['options'])) {
                $elementSpec['spec']['options'] = array_merge($elementSpec['spec']['options'], $options);
            }
            if ($elementSpec['spec']['options'] instanceof ArrayObject) {
                $elementSpec['spec']['options'] = array_merge($elementSpec['spec']['options']->getArrayCopy(), $options);
            }
        } else {
            $elementSpec['spec']['options'] = $options;
        }
    }
}