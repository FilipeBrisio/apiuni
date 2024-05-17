<?php

namespace App\Entity;

use App\Repository\PedidosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidosRepository::class)]
class Pedidos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    private ?Cliente $cpfCliente = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    private ?Produtos $produtosPedidos = null;

    #[ORM\ManyToOne(inversedBy: 'pedidos')]
    private ?Transporte $placaCarro = null;

    #[ORM\Column(length: 255)]
    private ?string $forma_de_pagamento = null;

    #[ORM\Column]
    private ?float $Total = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpfCliente(): ?Cliente
    {
        return $this->cpfCliente;
    }

    public function setCpfCliente(?Cliente $cpfCliente): static
    {
        $this->cpfCliente = $cpfCliente;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getProdutosPedidos(): ?Produtos
    {
        return $this->produtosPedidos;
    }

    public function setProdutosPedidos(?Produtos $produtosPedidos): static
    {
        $this->produtosPedidos = $produtosPedidos;

        return $this;
    }

    public function getPlacaCarro(): ?Transporte
    {
        return $this->placaCarro;
    }

    public function setPlacaCarro(?Transporte $placaCarro): static
    {
        $this->placaCarro = $placaCarro;

        return $this;
    }

    public function getFormaDePagamento(): ?string
    {
        return $this->forma_de_pagamento;
    }

    public function setFormaDePagamento(string $forma_de_pagamento): static
    {
        $this->forma_de_pagamento = $forma_de_pagamento;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->Total;
    }

    public function setTotal(float $Total): static
    {
        $this->Total = $Total;

        return $this;
    }
}
