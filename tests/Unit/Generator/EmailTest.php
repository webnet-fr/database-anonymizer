<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\Unit\Generator;

use Faker\Provider\Internet;
use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Generator\Email as EmailGenerator;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class EmailTest extends TestCase
{
    public function testAddress()
    {
        $internetProviderMock = $this->createMock(Internet::class);
        $internetProviderMock->method('email')
            ->willReturn('comptewebnet@webnet.fr');
        $generator = new EmailGenerator($internetProviderMock);

        $this->assertEquals('comptewebnet@webnet.fr', $generator->generate());
    }
}
