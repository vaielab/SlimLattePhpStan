<?php

namespace App\Controllers;

use Latte\Engine;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Test01 extends MyController
{
    public function __construct(private Engine $engine)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        $items = ['one', 'two', 'three', 'Héllo', 'a@\'t"esté'];
        $string = $this->engine->renderToString(__DIR__ . '/../templates/Test01.latte', ['items' => $items, 'title' => 'From Test01']);
        $response->getBody()->write($string);
        return $response;
    }
}
