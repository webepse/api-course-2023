<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceIncrementationController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    // public function __construct(
    //     private EntityManagerInterface $manager
    // ){}
   
    public function __invoke(Invoice $data)
    {
        $data->setChrono($data->getChrono() + 1);
        $this->manager->persist($data);
        $this->manager->flush();
        return $data;
    }

}