<?php

namespace App\Controllers;

use App\Renderer\TemplateRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Test02 extends MyController
{
    public function __construct(private TemplateRenderer $renderer)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        $items = ['one', 'two', 'three', 'Héllo', 'a@\'t"esté'];

        return $this->renderer->template($response, __DIR__ . '/../templates/Test02.latte', ['items' => $items, 'title' => 'From Test02']);

        
    }
}
