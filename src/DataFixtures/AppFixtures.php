<?php

namespace App\DataFixtures;

use App\Entity\Sweatshirt;
use App\Entity\Stock;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];

        $sweatshirts = [
            ['name' => 'Blackbelt', 'price' => 29.90, 'image' => 'blackbelt.jpeg', 'featured' => true],
            ['name' => 'BlueBelt', 'price' => 29.90, 'image' => 'bluebelt.jpeg', 'featured' => false],
            ['name' => 'Street', 'price' => 34.50, 'image' => 'street.jpeg', 'featured' => false],
            ['name' => 'Pokeball', 'price' => 45.00, 'image' => 'pokeball.jpeg', 'featured' => true],
            ['name' => 'PinkLady', 'price' => 29.90, 'image' => 'pinklady.jpeg', 'featured' => false],
            ['name' => 'Snow', 'price' => 32.00, 'image' => 'snow.jpeg', 'featured' => false],
            ['name' => 'Greyback', 'price' => 28.50, 'image' => 'greyback.jpeg', 'featured' => false],
            ['name' => 'BlueCloud', 'price' => 45.00, 'image' => 'bluecloud.jpeg', 'featured' => false],
            ['name' => 'BornInUsa', 'price' => 59.90, 'image' => 'borninusa.jpeg', 'featured' => true],
            ['name' => 'GreenSchool', 'price' => 42.20, 'image' => 'greenschool.jpeg', 'featured' => false],
        ];

        foreach ($sweatshirts as $data) {
            $sweatshirt = new Sweatshirt();
            $sweatshirt->setName($data['name']);
            $sweatshirt->setPrice($data['price']);
            $sweatshirt->setImage($data['image']);
            $sweatshirt->setFeatured($data['featured']);

            foreach ($sizes as $size) {
                $stock = new Stock();
                $stock->setSize($size);
                $stock->setQuantity(random_int(2, 10));
                $stock->setSweatshirt($sweatshirt);
                $manager->persist($stock);
            }

            $manager->persist($sweatshirt);
        }

        // Utilisateur administrateur de test
        $admin = new User();
        $admin->setEmail('admin@stubborn.com');
        $admin->setName('Admin Stubborn');
        $admin->setDeliveryAddress('Piccadilly Circus, London W1J 0DA');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin1234'));
        $manager->persist($admin);

        $manager->flush();
    }
}