<?php

namespace DatabaseAnonymizer\Tests\System\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use WebnetFr\DatabaseAnonymizer\Command\AnonymizeCommand;
use WebnetFr\DatabaseAnonymizer\Command\GuessConfigCommand;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesser;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigWriter;
use WebnetFr\DatabaseAnonymizer\DependencyInjection\WebnetFrDatabaseAnonymizerExtension;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ChainGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\ConstantGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\FakerGeneratorFactory;
use WebnetFr\DatabaseAnonymizer\GeneratorFactory\GeneratorFactoryInterface;

/**
 * @see WebnetFrDatabaseAnonymizerExtension
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class WebnetFrDatabaseAnonymizerExtensionTest extends AbstractExtensionTestCase
{
    public function testServicesLoaded()
    {
        $this->load();

        $this->assertContainerBuilderHasService(ConstantGeneratorFactory::class);
        $this->assertContainerBuilderHasService(FakerGeneratorFactory::class);
        $this->assertContainerBuilderHasService(ChainGeneratorFactory::class);
        $this->assertContainerBuilderHasService(AnonymizeCommand::class);
        $this->assertContainerBuilderHasService(ConfigGuesser::class);
        $this->assertContainerBuilderHasService(ConfigWriter::class);
        $this->assertContainerBuilderHasService(GuessConfigCommand::class);

        $autoconfiguredInstanceof = $this->container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey(GeneratorFactoryInterface::class, $autoconfiguredInstanceof);
        $definition = $autoconfiguredInstanceof[GeneratorFactoryInterface::class];
        /** @var $definition ChildDefinition */
        $this->assertEquals(
            ['database_anonymizer.generator_factory' => [[]]],
            $definition->getTags()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions() : array
    {
        return [
            new WebnetFrDatabaseAnonymizerExtension(),
        ];
    }
}
