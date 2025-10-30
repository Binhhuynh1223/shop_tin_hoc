<?php

namespace App\Controllers;

class BaseController
{
    /**
     * Send JSON response
     * @param mixed $data
     * @param int $statusCode
     * @return void
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to a URL
     * @param string $url
     * @param string|null $message
     * @param string $type
     * @return void
     */
    protected function redirect($url, $message = null, $type = 'success')
    {
        if ($message) {
            $_SESSION["{$type}_message"] = $message;
        }
        header("Location: $url");
        exit;
    }

    /**
     * Render a view
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . "/../Views/$view.php";
    }
}
