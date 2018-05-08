<?php

namespace App\Api\V1\Controllers;

use App\Hashtag;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Used temporarily to send messages in json response
     *
     * @var string
     */
    protected $debugMessage = [];

    /**
     * If you use this method there will be another property in json called debug
     *
     * @param array $debugData
     */
    public function dLog(array $debugData)
    {
        $this->debugMessage[] = $debugData;
    }

    public function dLogWithTime($message)
    {
        $this->debugMessage[] = [
            'message' => $message,
            'time' => microtime(true),
        ];
    }

    protected function authUser()
    {
        return \Auth::user();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|mixed $model
     *
     * @return bool
     */
    protected function belongsToAuthUser($model)
    {
        if (!isset($model->user_id)) {
            return false;
        }
        return $model->user_id === $this->authUser()->id;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param  array $jsonData
     * @param  array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($jsonData = [], $headers = [])
    {
        // Some custom debugging, like console logging directly in json response
        if ($this->debugMessage) {
            $jsonData['debug'] = $this->debugMessage;
        }

        return response()->json($jsonData, $this->getStatusCode(), $headers);
    }

    public function respondWithData($data)
    {
        return $this->respond([
          'success' => true,
          'data'    => $data,
        ]);
    }

    public function respondWithError($message)
    {
        return $this->respond([
          'success' => false,
          'error'   => [
            'message'     => $message,
            'status_code' => $this->statusCode,
          ],
        ]);
    }

    public function respondWithMessage($message, $success = true)
    {
        return $this->respond([
          'success' => $success,
          'message' => $message,
        ]);
    }

    public function respondSuccess()
    {
        return $this->setStatusCode(200)->respond();
    }

    public function respondCreated($message = 'Resource created')
    {
        return $this->setStatusCode(201)->respondWithMessage($message);
    }

    public function respondWrongArgs($message = 'Wrong args')
    {
        return $this->setStatusCode(422)->respondWithError($message);
    }

    public function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }

    public function respondForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

//    public function respondNotAllowed($message = 'Method Not Allowed')
//    {
//        return $this->setStatusCode(405)->respondWithError($message);
//    }

    public function respondInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

}