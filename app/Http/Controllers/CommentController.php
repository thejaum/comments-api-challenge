<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Services\CommentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    private $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public function getAll(Request $request)
    {
        try {
            $id_user_query_string = $request->query('id_user');
            $id_post_query_string = $request->query('id_post');
            return response()->json(
                $this->service->getAll($id_user_query_string,$id_post_query_string), 
                Response::HTTP_OK);
        } catch (CustomValidationException $e) {
            return $this->error($e->getMessage(), $e->getDetails(),$e->getCode());
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    public function store(Request $request)
    {
        try {
            return response()->json(
                $this->service->store($request->all()), 
                Response::HTTP_CREATED
            );
        } catch (CustomValidationException $e) {
            return $this->error($e->getMessage(), $e->getDetails(),$e->getCode());
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id,Request $request)
    {
        try {
            $id_user_query_string = $request->header('id_user');                
                $this->service->delete($id,$id_user_query_string);
                return response()->json(['message'=>'Comment has been deleted.'],
                Response::HTTP_OK);
        } catch (CustomValidationException $e) {
            return $this->error($e->getMessage(), $e->getDetails(),$e->getCode());
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
