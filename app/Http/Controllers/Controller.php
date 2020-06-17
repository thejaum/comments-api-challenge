<?php

namespace App\Http\Controllers;

use Exception;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{

    public function error($message, $errors = [],$code=500)
    {
    	return response()->json(
    		['message' => $message, 'errors' => $errors],
    		$code
    	);
    }
    
}
