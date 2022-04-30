<?php

namespace App\DataFixtures;

use App\Entity\Transactions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TransaFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $transa = new Transactions();
         $transa->setName('BitCoin')
         ->setPrice(1520)
         ->setCreatedAt(new \DateTime())
         ->setQuantity(10)
         ->setSolded(false);

         $manager->persist($transa);

         $manager->flush();
    }
}
