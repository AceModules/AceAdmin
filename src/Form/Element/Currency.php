<?php

namespace AceAdmin\Form\Element;

use Laminas\Form\Element;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\Regex as RegexValidator;
use Laminas\Validator\ValidatorInterface;

class Currency extends Element implements InputProviderInterface
{
    const CURRENCY_FORMAT = '^\$\d{1,3}(\.\d{2})?$';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        'currencyFormat' => "Must be in US currency including dollar sign",
    );

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('type', 'text');
        $this->setAttribute('pattern', self::CURRENCY_FORMAT);
        $this->setAttribute('data-mask', '$000.00');
    }

    /**
     * @return RegexValidator
     */
    public function getValidator()
    {
        if (null === $this->validator) {
            $validator = new RegexValidator('/' . self::CURRENCY_FORMAT . '/');
            $validator->setMessage($this->messageTemplates['currencyFormat'], RegexValidator::NOT_MATCH);

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
                array('name' => 'Laminas\Filter\StringTrim'),
            ),
            'validators' => array(
                $this->getValidator(),
            ),
        );
    }
}