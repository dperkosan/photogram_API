<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;


/**
 * Api Controller
 */
class ApiController extends Controller
{
    /**
     * @var integer
     */
    protected $statusCode = 200;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param  $data
     * @param  array $headers
     * @return mixed
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param  string $message
     * @return mixed
     */
    public function respondWithError($message)
    {
        return $this->respond([

            'error' => [
                'message'      => $message,
                'status_code'  => $this->getStatusCode()
            ]

        ]);
    }

    /**
     * Respond - Success (Status Code - 200)
     *
     * @param  string $message
     * @return mixed
     */
    public function respondSuccess($message = 'Success')
    {
        return $this->setStatusCode(200)->respondWithError($message);
    }

    /**
     * Respond - Created (Status Code - 201)
     *
     * @param  string $message
     * @return mixed
     */
    public function respondCreated($message = 'Wrong Args')
    {
        return $this->setStatusCode(201)->respondWithError($message);
    }

    /**
     * Respond - Wrong Arguments (Status Code - 400)
     *
     * @param  string $message
     * @return mixed
     */
    public function respondWrongArgs($message = 'Wrong Args')
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    /**
     * Respond - Unauthorized (Status Code - 401)
     *
     * @param  string $message
     * @return mixed
     */
    public function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }

    /**
     * Respond - Forbidden (Status Code - 403)
     *
     * @param  string $message
     * @return mixed
     */
    public function respondForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    /**
     * Respond - Not Found (Status Code - 404)
     *
     * @param  string $message
     * @return json
     */
    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * Respond - Method Not Allowed (Status Code - 405)
     *
     * @param  string $message
     * @return mixed
     */
    public function respondNotAllowed($message = 'Method Not Allowed')
    {
        return $this->setStatusCode(405)->respondWithError($message);
    }

    /**
     * Respond - Internal Error (Status Code - 500)
     *
     * @param  string $message
     * @return json
     */
    public function respondInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

}