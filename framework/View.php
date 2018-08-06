<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/2 22:01
 */

namespace Mocha\Framework;

/**
 * Class View
 * @package Mocha\Framework
 */
class View
{
    /**
     * The name of the view.
     * @var string
     */
    protected $view;

    /**
     * The array of view data.
     * @var array
     */
    protected $data;

    /**
     * View constructor.
     * @param string $view
     * @param array $data
     */
    public function __construct($view, $data = [])
    {
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Get the string contents of the view.
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        $path = $this->getPath($this->view);

        $level = ob_get_level();

        ob_start();

        extract($this->data);

        try {
            include $path;
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }

        return ob_get_clean();
    }

    /**
     * Get the path to the view file.
     * @param string $view
     * @return string
     */
    protected function getPath($view)
    {
        $path = app_path() . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($path)) {
            return $path;
        }

        throw new \InvalidArgumentException("View [$view] not found.");
    }
}