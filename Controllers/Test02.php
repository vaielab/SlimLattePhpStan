<?php

namespace App\Controllers;

use Latte\Engine;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Test02 extends MyController
{
    public function __construct(private Engine $engine)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        $items = ['one', 'two', 'three', 'Héllo', 'a@\'t"esté'];
        $string = $this->engine->renderToString('Test02.latte', ['items' => $items, 'title' => 'From Test02']);
        $response->getBody()->write($string);
        return $response;
    }
}
