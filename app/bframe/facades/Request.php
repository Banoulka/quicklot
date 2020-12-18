<?php


namespace app\bframe\facades;

class Request
{
    public array $params;
    public ?string $from;
    public string $method;
    public string $url;
    public array $files;
    public object $post;

    public function __construct($server, $post, $get, $files)
    {
        $this->params = array_map(function ($item) {
            return htmlentities($item);
        }, $get);

        $this->from = $server['HTTP_REFERER'] ?? null;
        $this->method = $server['REQUEST_METHOD'];
        $this->url = $server['PATH_INFO'] ?? '/';
        $this->files = $files;
        $this->post = new \stdClass();

        // Add properties of post request on
        foreach ($post as $key => $value) {
            // TODO: Sanitize
            $this->post->$key = htmlentities($value);
        }
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function validate($keyValidations, $page)
    {
        $validator = new Validation();

        foreach ($keyValidations as $key => $validations)
        {
            if (!is_array($validations)) {
                $validations = explode('|', $validations);
            }

            foreach ($validations as $methodName) {
                // Split into params and method name
                $arr = preg_split('#:#', $methodName);

                // Validation method and value
                $method = $arr[0];
                $value = $this->post->$key;

                if (count($arr) == 1) {
                    // Call validation method
                    $validator->$method($key, $value);

                } else {
                    // Call validation method with params
                    $extra = $arr[1];
                    $validator->$method($key, $value, $extra);
                }
            }
        }

        $form = new Form($validator->getErrors());
        $data = $this->all();

        if (!$form->isValid()) {

            // If the form is not valid then render the last page
            Response::render($page, [
                'form' => $form,
                'data' => $data
            ])->status(400);

            exit();
        }
    }

    public function only($arr)
    {
        $only = [];
        // If the value is set then assign it and add it to the array
        foreach ($arr as $key) {
            if (isset($this->post->$key)) {
                $only[$key] = $this->post->$key;
            }
        }
        return $only;
    }

    public function all(): array
    {
        $all = [];
        foreach ($this->post as $key => $value)
        {
            $all[$key] = $value;
        }
        return $all;
    }
}