<?php

namespace helpers;

use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    protected $errors = [];

    public function validate(array $attributes, array $rules)
    {
        foreach ($rules as $field => $rule) {
            try {
                $value = $attributes[$field] ?? null;
                $rule->setName($field)->assert($value);
            } catch (NestedValidationException $e) {
                $message = $e->getMessages();
                if (!empty($message[$field])) {
                    $this->errors[] = $message[$field];
                }
            }
        }
        return $this->errors;
    }
}