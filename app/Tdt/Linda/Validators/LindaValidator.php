<?php namespace Tdt\Linda\Validators;

class LindaValidator extends \Illuminate\Validation\Validator {

    // Special validator for array values that all need to be URIs
    public function validateMultipleuri($attribute, $value, $parameters)
    {
        foreach ($value as $val) {
            if (filter_var($val, FILTER_VALIDATE_URL) === FALSE) {
                return false;
            }
        }

        return true;
    }
}
