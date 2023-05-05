<?php

namespace App\Renderer;

use Efabrica\PHPStanLatte\Collector\CollectedData\CollectedResolvedNode;
use Efabrica\PHPStanLatte\LatteContext\LatteContext;
use Efabrica\PHPStanLatte\LatteTemplateResolver\LatteTemplateResolverResult;
use Efabrica\PHPStanLatte\LatteTemplateResolver\NodeLatteTemplateResolverInterface;
use Efabrica\PHPStanLatte\Resolver\NameResolver\NameResolver;
use Efabrica\PHPStanLatte\Resolver\ValueResolver\ValueResolver;
use Efabrica\PHPStanLatte\Template\Template;
use Efabrica\PHPStanLatte\Template\TemplateContext;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;

final class TemplateResolver implements NodeLatteTemplateResolverInterface
{
    private const ACTUAL_CLASS = 'actual_class';

    private const ACTUAL_METHOD = 'actual_method';

    private const PATHS = 'paths';

    public function __construct(
        private NameResolver $nameResolver,
        private ValueResolver $valueResolver
    ) {
    }

    public function collect(Node $node, Scope $scope): array
    {
        if (!$node instanceof MethodCall) {
            return [];
        }

        if ($this->nameResolver->resolve($node) !== 'renderToString') {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (!$callerType instanceof ObjectType) {
            return [];
        }
        if (!$callerType->isInstanceOf('Latte\Engine')->yes()) {
            return [];
        }

        return [
            new CollectedResolvedNode(self::class, $scope->getFile(), [
                self::ACTUAL_CLASS => $scope->getClassReflection()->getName(),
                self::ACTUAL_METHOD => $scope->getFunctionName(),
                self::PATHS => $this->getPaths($node, $scope),
            ]),
        ];
    }

    public function resolve(CollectedResolvedNode $resolvedNode, LatteContext $latteContext): LatteTemplateResolverResult
    {
        $params = $resolvedNode->getParams();
        $paths = $params[self::PATHS] ?? [];

        $class = $params[self::ACTUAL_CLASS];
        $method = $params[self::ACTUAL_METHOD];

        $variables = $latteContext->variableFinder()->find($class, $method);

        $templates = [];
        foreach ($paths as $path) {
            $templateContext = new TemplateContext($variables);
            $templates[] = new Template($path, $class, $method, $templateContext);
        }

        return new LatteTemplateResolverResult($templates);
    }

    private function getPaths(MethodCall $methodCall, Scope $scope): array
    {
        $firstArg = $methodCall->getArgs()[0] ?? null;
        if ($firstArg === null) {
            return [];
        }

        $paths = $this->valueResolver->resolveStrings($firstArg->value, $scope);
        if ($paths === null) {
            return [];
        }

        $fullPaths = [];
        foreach ($paths as $path) {
            $fullPaths[] = __DIR__ . '/../templates/' . $path;
        }
        return $fullPaths;
    }
}

