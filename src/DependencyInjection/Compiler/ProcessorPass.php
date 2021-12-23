<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingBundle\DependencyInjection\Compiler;

use Patchlevel\EventSourcing\EventBus\DefaultEventBus;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function array_keys;

class ProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(DefaultEventBus::class)) {
            return;
        }

        foreach (array_keys($container->findTaggedServiceIds('event_sourcing.processor')) as $id) {
            $container->getDefinition(DefaultEventBus::class)->addMethodCall('addListener', [new Reference($id)]);
        }
    }
}