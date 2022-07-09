<?php

namespace App\Entity;

use App\Repository\ServicesProductsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServicesProductsRepository::class)
 */
class ServicesProducts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Services::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $service_id;

    /**
     * @ORM\OneToOne(targetEntity=Produits::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceId(): ?Services
    {
        return $this->service_id;
    }

    public function setServiceId(Services $service_id): self
    {
        $this->service_id = $service_id;

        return $this;
    }

    public function getProductId(): ?Produits
    {
        return $this->product_id;
    }

    public function setProductId(Produits $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }
}
