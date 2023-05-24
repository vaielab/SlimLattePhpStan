<?php

declare(strict_types=1);

namespace App\Renderer;

use Efabrica\PHPStanLatte\LatteContext\CollectedData\CollectedError;
use Efabrica\PHPStanLatte\LatteContext\CollectedData\CollectedTemplateRender;
use Efabrica\PHPStanLatte\LatteContext\Collector\AbstractLatteContextCollector;
use Efabrica\PHPStanLatte\LatteContext\Collector\AbstractLatteContextSubCollector;
use Efabrica\PHPStanLatte\LatteContext\Collector\TemplateRenderCollector\TemplateRenderCollectorInterface;
use Efabrica\PHPStanLatte\LatteContext\LatteContextHelper;
use Efabrica\PHPStanLatte\PhpDoc\LattePhpDocResolver;
use Efabrica\PHPStanLatte\Resolver\NameResolver\NameResolver;
use Efabrica\PHPStanLatte\Resolver\TypeResolver\TemplateTypeResolver;
use Efabrica\PHPStanLatte\Resolver\ValueResolver\PathResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;

/**
 * @extends AbstractLatteContextCollector<CollectedTemplateRender|CollectedError>
 */
final class TemplateRendererCollector extends AbstractLatteContextSubCollector implements TemplateRenderCollectorInterface
{
    private NameResolver $nameResolver;

    private PathResolver $pathResolver;

    private TemplateTypeResolver $templateTypeResolver;

    private LattePhpDocResolver $lattePhpDocResolver;

    public function __construct(
        NameResolver $nameResolver,
        PathResolver $pathResolver,
        TemplateTypeResolver $templateTypeResolver,
        LattePhpDocResolver $lattePhpDocResolver
    ) {
        $this->nameResolver = $nameResolver;
        $this->pathResolver = $pathResolver;
        $this->templateTypeResolver = $templateTypeResolver;
        $this->lattePhpDocResolver = $lattePhpDocResolver;
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function collect(Node $node, Scope $scope): ?array
    {
        $calledMethodName = $this->nameResolver->resolve($node);
        if (!in_array($calledMethodName, ['template'], true)) {
            return null;
        }

        if (!LatteContextHelper::isClass($node, $scope, 'App\Renderer\TemplateRenderer')
        ) {
            return null;
        }

        $templatePathExpr = $node->getArgs()[1]->value ?? null;
        $templateVariablesExpr = $node->getArgs()[2]->value ?? null;

        $paths = $this->pathResolver->resolve($templatePathExpr, $scope);

        $lattePhpDoc = $this->lattePhpDocResolver->resolveForNode($node, $scope);
        if ($lattePhpDoc->getTemplatePaths() !== []) {
            $paths = $lattePhpDoc->getTemplatePaths();
        }

        if ($paths === null) {
            return [CollectedError::build($node, $scope, 'Cannot automatically resolve latte template from expression.')];
        }

        $variables = LatteContextHelper::variablesFromExpr($templateVariablesExpr, $scope);

        return CollectedTemplateRender::buildAll($node, $scope, $paths, $variables);
    }
}
