<?php

namespace App\DataFixtures;

use App\Entity\Property;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasherInterface;

    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface) 
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User;
        $plainText = 'secret';
        $hashed = $this->userPasswordHasherInterface->hashPassword(
            $admin, $plainText
        );

        $admin
            ->setUserName('admin')
            ->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($hashed)
            ;

        $manager->persist($admin);  

        $user = new User;
        $plainText = 'secret';
        $hashed = $this->userPasswordHasherInterface->hashPassword(
            $user, $plainText
        );
        
        $user
            ->setUserName('john')
            ->setEmail('john.doe@example.com')
            ->setPassword($hashed)
            ->setRoles(['ROLE_USER'])
        ;
        $manager->persist($user);        

        for ($i=1; $i <=100 ; $i++) { 
            $property = new Property;
            $property
                ->setTitle($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setSurface($faker->numberBetween(20, 350))
                ->setRooms($faker->numberBetween(2, 10))
                ->setBedrooms($faker->numberBetween(1, 9))
                ->setFloor($faker->numberBetween(0, 15))
                ->setPrice($faker->numberBetween(100000, 1000000))
                ->setHeat($faker->numberBetween(0, count(Property::HEAT) - 1))
                ->setCity($faker->city())
                ->setAddress($faker->address())
                ->setPostalCode($faker->postcode())
                ->setSold(false);
            $manager->persist($property);
        }
        $manager->flush();
    }
}
