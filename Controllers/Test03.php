<?php

namespace App\Controllers;

use Latte\Engine;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Test03 extends MyController
{
    public function __construct(private Engine $engine)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        //$items = ['one', 'two', 'three', 'Héllo', 'a@\'t"esté'];

        // Missing $items variable

        $string = $this->engine->renderToString('Test03.latte', ['title' => 'From Test03']);
        $response->getBody()->write($string);
        return $response;
    }
}
