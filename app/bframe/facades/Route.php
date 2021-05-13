<?php

namespace app\bframe\facades;

class Route
{
    private static array $getRoutes = [];
    private static array $postRoutes = [];

    public string $method, $url;

    private function __construct($method, $url)
    {
        $this->method = $method;
        $this->url = $url;
    }

    public static function both($url, $function)
    {
        self::get($url, $function);
        self::post($url, $function);
        return new Route('BOTH', $url);
    }

    public static function get($url, $function)
    {
        Route::$getRoutes[$url] = ['function' => $function];
        return new Route('GET', $url);
    }

    public static function post($url, $function)
    {
        Route::$postRoutes[$url] = ['function' => $function];
        return new Route('POST', $url);
    }

    public function middleware($middleware)
    {
        if ($this->method === 'BOTH') {
            Route::$getRoutes[$this->url]['middleware'] = $middleware;
            Route::$postRoutes[$this->url]['middleware'] = $middleware;
        } else if ($this->method === 'GET') {
            Route::$getRoutes[$this->url]['middleware'] = $middleware;
        } else {
            Route::$postRoutes[$this->url]['middleware'] = $middleware;
        }
    }

    public static function resolve()
    {
        $request = new Request($_SERVER, $_POST, $_GET, $_FILES);

        // Unpick the url and method
        $url = $request->url;

        $route = $request->isGet()
            ? Route::$getRoutes[$url] ?? null
            : Route::$postRoutes[$url] ?? null;

        $fn = $route['function'] ?? null;

        // Call the controller functions or return none if page not found
        if ($route && $fn) {

            // Process Middleware
            if (isset($route['middleware'])) {
                $middleware = $route['middleware'];

                if (is_array($middleware)) {
                    foreach ($middleware as $name) {
                        call_user_func(["\app\bframe\middleware\\$name", "resolve"], $request);
                    }
                } else {
                    call_user_func(["\app\bframe\middleware\\$middleware", "resolve"], $request);
                }
            }

            // Call controller function
            call_user_func($fn, $request);
        } else {
            // Send error
            Response::render('_404')->status(404);
        }
    }

    public static function redirect($url)
    {
        header('Location: ' . $url);
    }
}