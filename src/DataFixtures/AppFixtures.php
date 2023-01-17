<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        $adminUser = new User();
        $adminUser->setFirstName("Jordan")
                ->setLastName("Berti")
                ->setEmail("berti@epse.be")
                ->setPassword($this->passwordHasher->hashPassword($adminUser,'password'))
                ->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminUser);

        //gestion des User
        for($u=0;$u<10;$u++)
        {
            $chrono=1;
            $user = new User();
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($this->passwordHasher->hashPassword($user,'password'));
            $manager->persist($user);
            // gestion des clients 
            for($c=0;$c<rand(5,20);$c++)
            {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                        ->setLastName($faker->lastName())
                        ->setCompany($faker->company())
                        ->setEmail($faker->email())
                        ->setUser($user);
                $manager->persist($customer);
                // gestion des factures
                for($i=0;$i<rand(3,10);$i++)
                {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2,250,5000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT','PAID','CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono);
                    $chrono++;
                    $manager->persist($invoice);
                }
            }
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
