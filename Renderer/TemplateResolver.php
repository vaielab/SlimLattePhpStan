<?php
namespace App\Renderer;

use Efabrica\PHPStanLatte\LatteTemplateResolver\AbstractClassMethodTemplateResolver;

final class TemplateResolver extends AbstractClassMethodTemplateResolver
{
    public function getSupportedClasses(): array
    {
        return ['object'];
    }

    protected function getClassNamePattern(): string
    {
        return '/^App\\\\(Controllers|Renderer)\\\\.*$/';
    }

    protected function getClassMethodPattern(): string
    {
        return '/^(index|template)$/';
    }
}
