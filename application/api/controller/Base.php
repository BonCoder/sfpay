<?php

namespace app\api\controller;
use think\Cache;
use think\Controller;
use think\exception\HttpException;
use think\response\Json;
use think\response\Jsonp;

class Base extends Controller
{
    /**
     * Base constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->request->method() == 'OPTIONS'){
            throw new HttpException('200', '');
        }
        $agent =  explode(' ', $this->request->header('Authorization'));
        $token = $agent[0].':'.$agent[1] ?? '';
        if (!$token || !Cache::has($token)) {
            throw new HttpException('401', 'Unauthenticated.');
        }
        $this->request->user = json_decode(Cache::get($token), false);
    }

    /**
     * send error json string
     * @param int $code
     * @param string $message
     * @param int $statusCode
     * @return Json
     */
    public function sendError($code = 0, $message = '', $statusCode = 422){

        $headers = ['content-type' => 'application/json'];
        return json(['code' => $code, 'msg' => $message], $statusCode)->header($headers);
    }

    /**
     * send success json string
     * @param array $data
     * @return Json|Jsonp
     */
    public function sendJson($data = [])
    {
        $headers = ['content-type' => 'application/json'];
        return json(['code' => 1, 'data' => $data, 'msg' => ''], 200)->header($headers);
    }


    /**
     * send success string
     * @param string $msg
     * @param int $statusCode
     * @return Json|Jsonp
     */
    public function sendSuccess($msg = '操作成功！', $statusCode = 201)
    {
        $headers = ['content-type' => 'application/json'];
        return json(['code' => 1, 'msg' => $msg], $statusCode)->header($headers);
    }
}