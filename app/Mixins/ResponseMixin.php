<?php

namespace App\Mixins;

class ResponseMixin
{

    public function successJson()
    {
        return function ($data, $code = 200, $msg = 'successfully', $status = true) {
            return [
                'result' => $status,
                'code' => $code,
                'data' => $data,
                'msg' => $msg
            ];
        };
    }

    public function errorJson()
    {
        return function ($data, $code = 404, $msg = 'fail', $status = false) {
            return [
                'result' => $status,
                'code' => $code,
                'data' => $data,
                'msg' => $msg
            ];
        };
    }
}
