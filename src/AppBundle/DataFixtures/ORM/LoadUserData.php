<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('sarah');
        $user1->setPlainPassword('pass');
        $user1->setEmail('sarah@exemple.org');
        $user1->setEnabled(true);
        $this->addReference('user_sarah', $user1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('toto');
        $user2->setPlainPassword('toto');
        $user2->setEmail('toto@exemple.org');
        $user2->setEnabled(true);
        $this->addReference('user_toto', $user2);
        $manager->persist($user2);

        $adm = new User();
        $adm->setUsername('adm');
        $adm->setPlainPassword('mdp4adm');
        $adm->setEmail('adm@exemple.org');
        $adm->setEnabled(true);
        $adm->setRoles(array('ROLE_ADMIN'));
        $this->addReference('user_adm', $adm);
        $manager->persist($adm);

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 5;
    }
    
}