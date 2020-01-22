<?php

namespace App\DataFixtures;

use App\Entity\Rubrique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RubriqueFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 5; $i++){
            $rubrique = new Rubrique();
            $rubrique->setLibelle("libelle de la rubrique n°$i");

            $manager->persist($rubrique);
        }

        $manager->flush();
    }
}
