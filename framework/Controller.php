<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 15:15
 */

namespace Mocha\Framework;

/**
 * Class Controller
 * @package Mocha\Framework
 */
class Controller
{
    /**
     * @return Request
     */
    public function request()
    {
        return app(Request::class);
    }

    public function response()
    {

    }

    /**
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @throws \Exception
     */
    public function validate(Request $request, array $rules, array $messages = [])
    {
        $validator = new Validator($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new \Exception(json_encode($validator->getErrors()));
        }
    }
}