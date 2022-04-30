<?php

namespace App\Tests\Entity;

use App\DataFixtures\TransaFixture;
use App\Entity\Transactions;
use App\Repository\TransactionsRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransactionTest extends KernelTestCase
{
    public function testDonneeTransa(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $routerService = static::getContainer()->get(TransactionsRepository::class)->count([]);

        $this->assertEquals(1, $routerService);
    }
}
