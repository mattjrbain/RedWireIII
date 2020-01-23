<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Annonce;
use App\Entity\Rubrique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AnnonceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        //RUBRIQUE

        for($i = 1; $i <= 5; $i++){
            $rubrique = new Rubrique();
            $rubrique->setLibelle($faker->word());
            $manager->persist($rubrique);
        }

        //UTILISATEUR

        $user = new User();
        $user->setEmail($faker->email())
                    ->setRoles([])
                    ->setPassword($faker->Password())
                    ->setFirstName($faker->Firstname())
                    ->setLastName($faker->LastName());

        $manager->persist($user);

        //ANNONCE

        for($j = 1; $j <= mt_rand(4,6); $j++){

            $annonce = new Annonce();
            $annonce->setEntete($faker->sentence())
                    ->setCorps($faker->word())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setExpiredAt($faker->dateTimeInInterval($faker->dateTimeBetween('-' . (new \DateTime())->diff($annonce->getCreatedAt())->days . ' days'), '+ 21 days'))                    ->setRubrique($rubrique)
                    ->setUser($user);

            $manager->persist($annonce);
        }

        $manager->flush();

    }
}
