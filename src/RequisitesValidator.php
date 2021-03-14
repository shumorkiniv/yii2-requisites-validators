<?php

namespace shumorkiniv\validators;

use yii\validators\Validator;

/**
 * Class RequisitesValidator
 *
 * The basic class for requisites validators.
 *
 * @author Shumorkin Ilya <shumorkinilya@mail.ru>
 */
abstract class RequisitesValidator extends Validator
{
    /** @var ?string User wrong count message */
    public ?string $wrongLengthMessage = null;
    /** @var ?string User wrong character message */
    public ?string $wrongCharMessage = null;

    /** @var array */
    protected array $numbers;
    /** @var int */
    protected int $length;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $defaultErrorMessages = $this->getDefaultErrorMessages();

        if ($this->wrongLengthMessage === null) {
            $this->wrongLengthMessage = $defaultErrorMessages['wrongLength'];
        }

        if ($this->wrongCharMessage === null) {
            $this->wrongCharMessage = $defaultErrorMessages['wrongChar'];
        }

        if ($this->message === null) {
            $this->message = $defaultErrorMessages['invalidValue'];
        }
    }


    /**
     * Returns array of default error messages
     *
     * The returns array the array is as follows:
     *
     * ['wrongLength' => 'wrongLength text', 'wrongChar' => 'wrongChar text', 'invalidValue' => 'invalidValue text']
     *
     * @return string[]
     */
    protected abstract function getDefaultErrorMessages(): array;

    /**
     * Returns true if value is in valid length range
     *
     * @param string $value
     * @return bool
     */
    protected abstract function checkValueLength(string $value): bool;

    /**
     * Returns true if value instance is string or int
     *
     * @param mixed $value
     * @return bool
     */
    protected function checkValueInstanceOf($value): bool
    {
        return is_int($value) || is_string($value);
    }

    /**
     * Returns true if value is numeric
     *
     * @param string $value
     * @return bool
     */
    protected function checkIsValuesCharsValid(string $value): bool
    {
        return is_numeric($value);
    }

    /**
     * Set properties numbers and length for next calculations
     *
     * @param string $value
     */
    protected function prepareData(string $value) {
        $this->numbers = array_map('intval', str_split($value));
        $this->length = count($this->numbers);
    }

    /**
     * Make preparatory actions before validation.
     *
     * Returns empty array if no errors.
     *
     * @param mixed $value
     * @return array
     */
    protected function preValidate($value): array
    {
        if (!$this->checkValueInstanceOf($value)) {
            return [$this->message, []];
        }

        if (is_int($value)) {
            $value = (string)$value;
        }

        if (!$this->checkValueLength($value)) {
            return [$this->wrongLengthMessage, []];
        }

        if (!$this->checkIsValuesCharsValid($value)) {
            return [$this->wrongCharMessage, []];
        }

        $this->prepareData($value);

        return [];
    }
}
