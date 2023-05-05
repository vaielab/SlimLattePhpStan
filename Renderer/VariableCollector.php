<?php

declare(strict_types=1);

namespace App\Renderer;

use Efabrica\PHPStanLatte\LatteContext\CollectedData\CollectedVariable;
use Efabrica\PHPStanLatte\LatteContext\Collector\AbstractLatteContextSubCollector;
use Efabrica\PHPStanLatte\LatteContext\Collector\VariableCollector\VariableCollectorInterface;
use Efabrica\PHPStanLatte\LatteContext\LatteContextHelper;
use Efabrica\PHPStanLatte\Resolver\NameResolver\NameResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;

final class VariableCollector extends AbstractLatteContextSubCollector implements VariableCollectorInterface
{
    public function __construct(
        private NameResolver $nameResolver
    ) {
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

        $secondArg = $node->getArgs()[1] ?? null;
        if ($secondArg === null) {
            return [];
        }

        $variables = [];
        foreach (LatteContextHelper::variablesFromType($scope->getType($secondArg->value)) as $variable) {
            $variables[] = CollectedVariable::build($node, $scope, $variable->getName(), $variable->getType());
        }
        return $variables;
    }
}
