<?php

namespace UJM\ExoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QuestionSerializerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('ujm_exo.serializer.question_collector')) {
            return;
        }

        $definition = $container->findDefinition('ujm_exo.serializer.question_collector');
        $taggedServices = $container->findTaggedServiceIds('ujm_exo.question.serializer');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
