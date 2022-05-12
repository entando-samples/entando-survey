<?php

namespace App\Firebase;

use Exception;
use \Illuminate\Http\Client\Response;

class PushNotificationFailed extends Exception
{
    protected $response = null;

    public function __construct(Response $response)
    {
        parent::__construct("Error while sending push notification");
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
