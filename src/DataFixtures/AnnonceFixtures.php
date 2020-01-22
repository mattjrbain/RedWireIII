<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Rubrique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AnnonceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for($i = 1; $i <= 5; $i++){
            $rubrique = new Rubrique();
            $rubrique->setLibelle("libelle de la rubrique n°$i");
        }

        for($j = 1; $j <= 5; $j++){
            $annonce = new Annonce();
            $annonce->setEntete("entete de l'annonce n°$j");
            $annonce->setCorps("entete de l'annonce n°$j");
            $annonce->setCreatedAt(new \DateTime());
            $annonce->setExpiredAt(new \DateTime());
            $annonce->setRubrique($rubrique);

            $manager->persist($annonce);
        }

        $manager->flush();

    }
}
