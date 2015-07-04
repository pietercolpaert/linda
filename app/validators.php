<?php
use Tdt\Linda\Validators\LindaValidator;

Validator::resolver(function ($translator, $data, $rules, $messages) {
    return new LindaValidator($translator, $data, $rules, $messages);
});