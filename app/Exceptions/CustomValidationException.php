<?php
/**
 * Define a custom exception class
 */

namespace App\Exceptions;

use Exception;

class CustomValidationException extends Exception
{

    public function __construct (string $message, array $details, int $code)
    {
        parent::__construct($message, $code);

        $this->details = $details;
        $this->code = $code;
    }
    
    public function getDetails()
    {
        return $this->details;
    }
    public function getStatusCode()
    {
        return $this->code;
    }
}
?>