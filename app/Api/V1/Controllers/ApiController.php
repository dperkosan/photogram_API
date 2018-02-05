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
    protected $debugMessage = '';

    /**
     * If you use this method there will be another property in json called debug
     *
     * @param $message
     */
    public function dLog($message)
    {
        if ($this->debugMessage) {
            $this->debugMessage .= '|';
        }
        $this->debugMessage .= $message;
    }

    protected function authUser()
    {
        return \Auth::user();
    }

    protected function addDataToUser($user)
    {
        $user->posts_count = \DB::table('posts')->where('user_id', $user->id)->count();
        $user->followers_count = \DB::table('followers')->where('followed_id', $user->id)->count();
        $user->following_count = \DB::table('followers')->where('follower_id', $user->id)->count();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|mixed $model
     *
     * @return bool
     */
    protected function belongsToAuthUser($model)
    {
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

    public function respondError($message)
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
        return $this->setStatusCode(400)->respondError($message);
    }

    public function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondError($message);
    }

    public function respondForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondError($message);
    }

    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(404)->respondError($message);
    }

    public function respondNotAllowed($message = 'Method Not Allowed')
    {
        return $this->setStatusCode(405)->respondError($message);
    }

    public function respondInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondError($message);
    }

}