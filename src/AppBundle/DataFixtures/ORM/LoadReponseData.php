<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Reponse;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class LoadReponseData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i=1;$i<=100;$i++){

            $ref_question = $this->getReference('question'.$i); // Référence livre courant
            $nb_reponses = mt_rand(0, 10); // Entre 0 et 10 critiques

            while ($nb_reponses > 0) { // On insère entre 0 et 10 critiques
                $reponse = new Reponse();

                $reponse->setBody($faker->text());
                $reponse->setVote(mt_rand(0, 10));
                $reponse->setQuestion($ref_question);

                $pile_face = rand(0,1);
                if($pile_face){
                 $user = $this->getReference('user_sarah');
                } else {
                    $user = $this->getReference('user_toto');
                }
                $reponse->setUser($user);

                $manager->persist($reponse);

                $nb_reponses--;
            }
        }

        $manager->flush();
    }
    function getOrder()
    {
        return 20;
    }
}