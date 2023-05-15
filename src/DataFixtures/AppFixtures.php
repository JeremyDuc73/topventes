<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0;$i<20;$i++) {
            $product = new Product();
            $product->setName("Carte Graphique");
            $product->setPrice(500);
            $product->setDescription("La nouvelle carte graphique de NVidia");
            $manager->persist($product);
        }
        $manager->flush();
    }
}
