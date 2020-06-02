<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Services\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    private $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function getAll(Request $request)
    {
        try {
            $id_user_query_string = $request->query('id_user');
            return response()->json(
                $this->service->getAll($id_user_query_string), 
                Response::HTTP_OK);
        } catch (CustomValidationException $e) {
            return $this->error($e->getMessage(), $e->getDetails(),$e->getCode());
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
?>