<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Annonce;
use App\Entity\Rubrique;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AnnonceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Rubriques

        for($i=1; $i<=5; $i++) {
            $rubrique = new Rubrique();
            $rubrique->setLibelle($faker->word());
            $manager->persist($rubrique);

            // Utilisateur

            $utilisateur = new User();
            $utilisateur->setEmail($faker->email())
                        ->setRoles([])
                        ->setPassword($faker->password())
                        ->setFirstName($faker->firstName())
                        ->setLastName($faker->lastName());
            $manager->persist($utilisateur);
            
            // Annonces
            
            for($j=1; $j<=mt_rand(4, 6); $j++) {

                $annonce = new Annonce();
                $annonce->setEntete($faker->sentence())
                        ->setCorps('<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>')
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setExpiredAt($faker->dateTimeInInterval($faker->dateTimeBetween('-' . (new \DateTime())->diff($annonce->getCreatedAt())->days . ' days'), '+ 21 days'))
                        ->setRubrique($rubrique)
                        ->setUser($utilisateur);
                $manager->persist($annonce);

                for($k=1; $k<=3; $k++) {
                    $image = new Image();
                    $image->setSrc($faker->imageUrl(350, 150))
                        ->setAnnonce($annonce);
                    $manager->persist($image);
                }
            }
        }
        $manager->flush();
    }
}
