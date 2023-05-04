<?php

namespace App\Renderer;

use Latte\Engine;
use Psr\Http\Message\ResponseInterface;

final class TemplateRenderer
{
	public function __construct(
		private Engine $engine,
	) {
	}

	public function template(
		ResponseInterface $response,
		string $template,
		array $data = [],
	): ResponseInterface
	{
		$string = $this->engine->renderToString($template, $data);
		$response->getBody()->write($string);

		return $response;
	}
}
