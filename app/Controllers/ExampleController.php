<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/2 15:01
 */

namespace Mocha\App\Controllers;

use Mocha\Framework\Controller;
use Mocha\Framework\Facades\Log;

class ExampleController extends Controller
{
    public function index()
    {
        return ['title' => 'Hello World!'];
    }

    public function test()
    {
        $this->validate(request()->all(), [
            'title' => 'required|numeric|min:3|max:5',
            'email' => 'required|email',
        ]);
        var_dump(request()->all());
        Log::info(json_encode(request()->all()));
    }
}
