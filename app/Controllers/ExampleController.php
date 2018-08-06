<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/2 15:01
 */

namespace Mocha\App\Controllers;

use Mocha\Framework\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        return ['title' => 'Hello World!'];
    }

    public function test()
    {
        $this->validate($this->request(), [
            'title' => 'required|numeric|min:3|max:5',
            'email' => 'required|email',
        ]);
        var_dump($this->request()->all());
    }
}
