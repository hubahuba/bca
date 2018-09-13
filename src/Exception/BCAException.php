<?php

namespace Ngungut\Bca\Exception;

use Exception;

class BCAException extends Exception
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'An error occurred';
}