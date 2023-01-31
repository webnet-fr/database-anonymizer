<?php

namespace DatabaseAnonymizer\Tests\System\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use WebnetFr\DatabaseAnonymizer\DependencyInjection\Compiler\ChainGeneratorFactoryPass;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ChainGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ConstantGeneratorFactory;

/**
 * @see ChainGeneratorFactoryPass
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ChainGeneratorFactoryPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @inheritdoc
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ChainGeneratorFactoryPass());
    }

    public function testPass()
    {
        $collectingService = new Definition();
        $this->setDefinition(ChainGeneratorFactory::class, $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('database_anonymizer.generator_factory');
        $this->setDefinition(ConstantGeneratorFactory::class, $collectedService);

        $nonCollectedService = new Definition();
        $this->setDefinition('non_collected_service', $nonCollectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            ChainGeneratorFactory::class,
            'addFactory',
            [
                new Reference(ConstantGeneratorFactory::class),
            ]
        );
    }
}
