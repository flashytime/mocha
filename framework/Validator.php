<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/5 21:18
 */

namespace Mocha\Framework;

class Validator
{
    /**
     * The data under validation.
     * @var array
     */
    protected $data;

    /**
     * The rules to be applied to the data.
     * @var array
     */
    protected $rules;

    /**
     * The array of validation messages.
     * @var array
     */
    protected $messages = [
        'required' => 'The %s field is required.',
        'numeric' => 'The %s must be a number.',
        'integer' => 'The %s must be an integer.',
        'string' => 'The %s must be a string.',
        'boolean' => 'The %s field must be true or false.',
        'array' => 'The %s must be an array.',
        'json' => 'The %s must be a valid JSON string.',
        'ip' => 'The %s must be a valid IP address.',
        'email' => 'The %s must be a valid email address.',
        'url' => 'The %s format is invalid.',
        'alpha' => 'The %s may only contain letters.',
        'alpha_num' => 'The %s may only contain letters and numbers.',
        'size' => 'The %s(or its length) must be %d.',
        'min' => 'The %s(or its length) must be at least %d.',
        'max' => 'The %s(or its length) may not be greater than %d.',
        'between' => 'The %s(or its length) must be between %d and %d.',
        'in' => 'The selected %s is invalid.',
        'not_in' => 'The selected %s is invalid.'
    ];

    /**
     * The array of validation errors
     * @var array
     */
    protected $errors = [];

    /**
     * Validator constructor.
     * @param array $data
     * @param array $rules
     * @param array $messages
     */
    public function __construct(array $data = [], array $rules = [], array $messages = [])
    {
        $this->data = $data;
        $this->messages = array_merge($this->messages, $messages);
        $this->setRules($rules);
    }

    /**
     * Determine if the data passes the validation rules.
     * @return bool
     */
    public function passes()
    {
        $this->validate();

        return empty($this->errors);
    }

    /**
     * Determine if the data fails the validation rules.
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Get the validation errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the validation rules.
     * @param array $rules
     * @return void
     */
    public function setRules(array $rules)
    {
        foreach ($rules as $key => $rule) {
            $rules[$key] = explode('|', $rule);
        }
        $this->rules = $rules;
    }

    /**
     * Run the validator's rules against its data.
     * @return void
     */
    public function validate()
    {
        $this->errors = [];
        foreach ($this->rules as $attribute => $rules) {
            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);
                //Check if we should stop further validations on a given attribute.
                if (isset($this->errors[$attribute])) {
                    break;
                }
            }
        }
    }

    /**
     * Validate a given attribute against a rule.
     * @param string $attribute
     * @param string $rule
     * @return void
     */
    protected function validateAttribute($attribute, $rule)
    {
        $parameters = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $parameter) = explode(':', $rule, 2);
            $parameters = explode(',', $parameter);
        }

        $method = 'validate' . str_replace(' ', '', ucwords(str_replace('_', ' ', $rule)));
        if (!$this->$method(array_get($this->data, $attribute), $parameters)) {
            $this->addError($attribute, $rule, $parameters);
        }
    }

    /**
     * Add an error message to the validator's errors.
     * @param string $attribute
     * @param string $rule
     * @param array $parameters
     * @return void
     */
    protected function addError($attribute, $rule, $parameters)
    {
        $message = $this->messages[$rule];
        array_unshift($parameters, $attribute);
        $this->errors[$attribute] = sprintf($message, ...$parameters);
    }

    /**
     * Validate the required attribute exists.
     * @param mixed $value
     * @return bool
     */
    protected function validateRequired($value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && empty($value)) {
            return false;
        }

        return true;
    }

    /**
     * Validate the attribute is numeric.
     * @param mixed $value
     * @return bool
     */
    protected function validateNumeric($value)
    {
        return is_numeric($value);
    }

    /**
     * Validate the attribute is an integer.
     * @param mixed $value
     * @return bool
     */
    protected function validateInteger($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate the attribute is a string.
     * @param mixed $value
     * @return bool
     */
    protected function validateString($value)
    {
        return is_string($value);
    }

    /**
     * Validate the attribute is a boolean.
     * @param mixed $value
     * @return bool
     */
    protected function validateBoolean($value)
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    /**
     * Validate the attribute is an array.
     * @param mixed $value
     * @return bool
     */
    protected function validateArray($value)
    {
        return is_array($value);
    }

    /**
     * Validate the attribute is a valid JSON string.
     * @param mixed $value
     * @return bool
     */
    protected function validateJson($value)
    {
        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Validate the attribute is a valid IP.
     * @param mixed $value
     * @return bool
     */
    protected function validateIp($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate the attribute is a valid e-mail address.
     * @param mixed $value
     * @return bool
     */
    protected function validateEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate the attribute is a valid URL.
     * @param mixed $value
     * @return bool
     */
    protected function validateUrl($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate the attribute contains only alphabetic characters.
     * @param mixed $value
     * @return bool
     */
    protected function validateAlpha($value)
    {
        return is_string($value) && preg_match('/^[a-zA-Z]+$/', $value);
    }

    /**
     * Validate then attribute contains only alpha-numeric characters.
     * @param mixed $value
     * @return bool
     */
    protected function validateAlphaNum($value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        return preg_match('/^[a-zA-Z0-9]+$/', $value) > 0;
    }

    /**
     * Validate the size of an attribute.
     * @param mixed $value
     * @param array $parameters
     * @return bool
     */
    protected function validateSize($value, $parameters)
    {
        $this->checkParameterCount(1, $parameters, 'size');

        return $this->getSize($value) == $parameters[0];
    }

    /**
     * Validate the size of an attribute is greater than a minimum value.
     * @param mixed $value
     * @param array $parameters
     * @return bool
     */
    protected function validateMin($value, $parameters)
    {
        $this->checkParameterCount(1, $parameters, 'min');

        return $this->getSize($value) >= $parameters[0];
    }

    /**
     * Validate the size of an attribute is less than a maximum value.
     * @param mixed $value
     * @param array $parameters
     * @return bool
     */
    protected function validateMax($value, $parameters)
    {
        $this->checkParameterCount(1, $parameters, 'max');

        return $this->getSize($value) <= $parameters[0];
    }

    /**
     * Validate the size of an attribute is between a set of values.
     * @param mixed $value
     * @param array $parameters
     * @return bool
     */
    protected function validateBetween($value, $parameters)
    {
        $this->checkParameterCount(2, $parameters, 'between');
        $size = $this->getSize($value);

        return $size >= $parameters[0] && $size <= $parameters[1];
    }

    /**
     * Validate an attribute is contained within a list of values.
     * @param mixed $value
     * @param array $parameters
     * @return bool
     */
    protected function validateIn($value, $parameters)
    {
        return !is_array($value) && in_array((string)$value, $parameters);
    }

    /**
     * Validate an attribute is not contained within a list of values.
     * @param mixed $value
     * @param array $parameters
     * @return bool
     */
    protected function validateNotIn($value, $parameters)
    {
        return !$this->validateIn($value, $parameters);
    }

    /**
     * Get the size of an attribute.
     * @param mixed $value
     * @return int
     */
    private function getSize($value)
    {
        if (is_numeric($value)) {
            return $value;
        } elseif (is_array($value)) {
            return count($value);
        }

        return mb_strlen($value);
    }

    /**
     * Check the certain number of parameters.
     * @param int $count
     * @param array $parameters
     * @param string $rule
     * @return void
     */
    private function checkParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new \InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }
}
