<?php

namespace App\Controllers;

use App\Renderer\TemplateRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Test03
{
    public function __construct(private TemplateRenderer $renderer)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        //$items = ['one', 'two', 'three', 'Héllo', 'a@\'t"esté'];

        // Missing $items variable

        return $this->renderer->template($response, 'Test03.latte', ['title' => 'From Test02']);

        
    }
}
