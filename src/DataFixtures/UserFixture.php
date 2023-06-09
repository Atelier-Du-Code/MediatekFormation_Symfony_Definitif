<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername("UtilisAdmin");
        $plaintextPassword = "zk@&AK67@czKyC&982";
        $HashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
        );
        
        $user->setPassword($HashedPassword);
        $user->setRoles(['ROLE_ADMIN']);        
        
        $manager->persist($user);
        $manager->flush();
    }
}
