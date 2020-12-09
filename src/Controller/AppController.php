<?php

namespace Mapping\Controller;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{
    /** 
     * @param array $datas
     * @param int $code = 200
     */
    protected function setResponseInJson(array $datas, int $code = 200): void
    {
        $body = $this->response->getBody();
        $body->write(($datas != null ) ? json_encode($datas) : json_encode([]));

        $this->response
            ->withStatus($code)
            ->withType('application/json')
            ->withBody($body);
    }
}
