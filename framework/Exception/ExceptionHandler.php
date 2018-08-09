<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/8 21:21
 */

namespace Mocha\Framework\Exception;

use Exception;
use Mocha\Framework\Response;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class ExceptionHandler
{
    /**
     * Report or log an exception.
     * @param Exception $e
     */
    public function report(Exception $e)
    {
        if (!$e instanceof ValidateException) {
            app('log')->error($e);
        }
    }

    /**
     * Render an exception into an HTTP response.
     * @param \Mocha\Framework\Request $request
     * @param Exception $e
     * @return \Mocha\Framework\Response
     */
    public function render($request, Exception $e)
    {
        $status = $e->getCode() ?: 500;

        if (config('app.debug')) {
            $whoops = new Run();
            $whoops->pushHandler(new PrettyPageHandler());

            return new Response($whoops->handleException($e), $status);
        }

        return new Response($e->getMessage(), $status);
    }
}