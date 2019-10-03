<?php

namespace AceAdmin\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Regex as RegexValidator;
use Zend\Validator\ValidatorInterface;

class Phone extends Element implements InputProviderInterface
{
    const PHONE_FORMAT = '^\(\d{3}\) \d{3}-\d{4}$';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('type', 'tel');
        $this->setAttribute('pattern', self::PHONE_FORMAT);
        $this->setAttribute('data-mask', '(000) 000-0000');
    }

    /**
     * @return RegexValidator
     */
    public function getValidator()
    {
        if (null === $this->validator) {
            $validator = new RegexValidator('/' . self::PHONE_FORMAT . '/');
            $validator->setMessage('Must be a 10-digit phone number with area code', RegexValidator::NOT_MATCH);

            $this->validator = $validator;
        }

        return $this->validator;
    }

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'validators' => array(
                $this->getValidator(),
            ),
        );
    }
}