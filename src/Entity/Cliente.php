<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomeCliente = null;

    #[ORM\Column(length: 255)]
    private ?string $telefoneCliente = null;

    #[ORM\Column(length: 255)]
    private ?string $cpfCliente = null;

    /**
     * @var Collection<int, Transporte>
     */
    #[ORM\OneToMany(targetEntity: Transporte::class, mappedBy: 'cliente')]
    private Collection $transportes;

    #[ORM\Column]
    private ?float $credito = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Pedidos>
     */
    #[ORM\OneToMany(targetEntity: Pedidos::class, mappedBy: 'cpfCliente')]
    private Collection $pedidos;

    public function __construct()
    {
        $this->transportes = new ArrayCollection();
        $this->pedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomeCliente(): ?string
    {
        return $this->nomeCliente;
    }

    public function setNomeCliente(string $nomeCliente): static
    {
        $this->nomeCliente = $nomeCliente;

        return $this;
    }

    public function getTelefoneCliente(): ?string
    {
        return $this->telefoneCliente;
    }

    public function setTelefoneCliente(string $telefoneCliente): static
    {
        $this->telefoneCliente = $telefoneCliente;

        return $this;
    }

    public function getCpfCliente(): ?string
    {
        return $this->cpfCliente;
    }

    public function setCpfCliente(string $cpfCliente): static
    {
        $this->cpfCliente = $cpfCliente;

        return $this;
    }

    /**
     * @return Collection<int, Transporte>
     */
    public function getTransportes(): Collection
    {
        return $this->transportes;
    }

    public function addTransporte(Transporte $transporte): static
    {
        if (!$this->transportes->contains($transporte)) {
            $this->transportes->add($transporte);
            $transporte->setCliente($this);
        }

        return $this;
    }

    public function removeTransporte(Transporte $transporte): static
    {
        if ($this->transportes->removeElement($transporte)) {
            // set the owning side to null (unless already changed)
            if ($transporte->getCliente() === $this) {
                $transporte->setCliente(null);
            }
        }

        return $this;
    }

    public function getCredito(): ?float
    {
        return $this->credito;
    }

    public function setCredito(float $credito): static
    {
        $this->credito = $credito;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Pedidos>
     */
    public function getPedidos(): Collection
    {
        return $this->pedidos;
    }

    public function addPedido(Pedidos $pedido): static
    {
        if (!$this->pedidos->contains($pedido)) {
            $this->pedidos->add($pedido);
            $pedido->setCpfCliente($this);
        }

        return $this;
    }

    public function removePedido(Pedidos $pedido): static
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getCpfCliente() === $this) {
                $pedido->setCpfCliente(null);
            }
        }

        return $this;
    }
}
