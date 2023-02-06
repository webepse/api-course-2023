<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\InvoiceRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\Link;
use App\Controller\InvoiceIncrementationController;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource(
    paginationEnabled: false,
    paginationItemsPerPage: 20,
    order: ['sentAt' => 'desc'],
    normalizationContext: [
        'groups' => ['invoices_read']
    ],
    operations: [
        new Get(),
        new Post(),
        new Post(
            controller: InvoiceIncrementationController::class,
            uriTemplate: '/invoices/{id}/increment',
            openapiContext:[
                'summary' => 'Incrémente une facture',
                'description' => "Incrémente le chrono d'une facture donnée"
            ]
        ),
        new GetCollection(),
        new Put(),
        new Delete(),
        new Patch()
    ],
    denormalizationContext:[
        "disable_type_enforcement"=>true
    ]
)]
#[ApiResource(
    uriTemplate: '/customers/{id}/invoices',
    uriVariables: [
        'id' => new Link(fromClass: Customer::class, fromProperty: 'invoices')
    ],
    operations: [new GetCollection()],
    normalizationContext: [
        'groups' => ['invoices_subresource']
    ]
)]
#[ApiFilter(OrderFilter::class, properties:['amount','sentAt'])]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoices_read','customers_read','invoices_subresource'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['invoices_read','customers_read','invoices_subresource'])]
    #[Assert\NotBlank(message: "Le montant est obligatoire")]
    #[Assert\Type(type:"numeric", message:"Le montat de la facture doit être au format numérique")]
    private $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['invoices_read','customers_read','invoices_subresource'])]
    #[Assert\NotBlank(message: "La date de la facture est obligatoire")]
    #[Assert\Type(type:"datetime", message:"La date doit être au format YYYY-MM-DD")]
    private $sentAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['invoices_read','customers_read','invoices_subresource'])]
    #[Assert\NotBlank(message: "Le statut de la facture est obligatoire")]
    #[Assert\Choice(choices:["SENT","PAID","CANCELLED"], message: "Le statut doit être soit SENT, soit PAID ou soit CANCELLED")]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['invoices_read'])]
    #[Assert\NotBlank(message: "Le client de la facture est obligatoire")]
    private ?Customer $customer = null;

    #[ORM\Column]
    #[Groups(['invoices_read','customers_read','invoices_subresource'])]
    #[Assert\NotBlank(message: "Le chrono de la facture est obligatoire")]
    #[Assert\Type(type:"integer",message:"Le chrono de la facture doit être au format numérique")]
    private $chrono = null;

    /**
     * Permet de récup le user à qui appartient finalement la facture
     *
     * @return User
     */
    #[Groups(['invoices_read'])]
    public function getUser(): User
    {
        return $this->customer->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt($sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono($chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
