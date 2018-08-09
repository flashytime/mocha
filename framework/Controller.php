<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 15:15
 */

namespace Mocha\Framework;

use Mocha\Framework\Exception\ValidateException;

/**
 * Class Controller
 * @package Mocha\Framework
 */
class Controller
{
    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return void
     * @throws \Exception
     */
    public function validate(array $data, array $rules, array $messages = [])
    {
        $validator = new Validator($data, $rules, $messages);
        if ($validator->fails()) {
            throw new ValidateException(json_encode($validator->getErrors()));
        }
    }
}
