<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 15:16
 */

namespace Mocha\Framework;

/**
 * Class Response
 * @package Mocha\Framework
 */
class Response
{
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string|array|object
     */
    protected $content;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * Response constructor.
     * @param string $content
     * @param int $status
     */
    public function __construct($content = '', $status = 200)
    {
        $this->setContent($content);
        $this->setStatusCode($status);
    }

    /**
     * Set the response status code.
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int)$statusCode;
    }

    /**
     * Set the response content.
     * @param mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        if (is_array($content)) {
            $this->setHeader('Content-Type', 'application/json');
            $content = json_encode($content);
        } elseif ($content instanceof View) {
            $content = $content->render();
        }
        $this->content = (string)$content;

        return $this;
    }

    /**
     * Set a header on the Response.
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Send HTTP headers and content.
     * @return void
     */
    public function send()
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo $this->content;
    }
}
