<?php

class Prefix extends Eloquent
{
    protected $table = 'prefixes';

    protected $fillable = ['prefix', 'uri'];
}
