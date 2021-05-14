<?php

namespace app\bframe\facades;

class Response
{
    public static function render($view, $args = []): Response
    {
        // Instantiate the local variables
        foreach ($args as $key => $value) {
            $$key = $value;
        }

        // If there is no form initialize an empty one
        $form = $form ?? Form::NoForm();

        // Cache the include and store in variable
        ob_start();
        include_once __DIR__ . "/../../../views/$view.phtml";
        $content = ob_get_clean();

        // Include the layout with access to content
        include_once __DIR__ . "/../../../views/_layout.phtml";

        // Return a new reponse
        return new Response();
    }

    public static function api($data) {
        header('Content-Type: application/json');

        echo json_encode($data);

        // Return a new reponse
        return new Response();
    }

    public function status($code)
    {
        // Change the http status code
        http_response_code($code);

        return Response::class;
    }
}