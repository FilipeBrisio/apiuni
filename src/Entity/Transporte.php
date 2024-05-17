<?php

namespace App\Entity;

use App\Repository\TransporteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransporteRepository::class)]
class Transporte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Tipo = null;

    #[ORM\Column(length: 255)]
    private ?string $placa = null;

    #[ORM\ManyToOne(inversedBy: 'transportes')]
    private ?Cliente $cliente = null;

    /**
     * @var Collection<int, Pedidos>
     */
    #[ORM\OneToMany(targetEntity: Pedidos::class, mappedBy: 'placaCarro')]
    private Collection $pedidos;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->Tipo;
    }

    public function setTipo(string $Tipo): static
    {
        $this->Tipo = $Tipo;

        return $this;
    }

    public function getPlaca(): ?string
    {
        return $this->placa;
    }

    public function setPlaca(string $placa): static
    {
        $this->placa = $placa;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): static
    {
        $this->cliente = $cliente;

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
            $pedido->setPlacaCarro($this);
        }

        return $this;
    }

    public function removePedido(Pedidos $pedido): static
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getPlacaCarro() === $this) {
                $pedido->setPlacaCarro(null);
            }
        }

        return $this;
    }
}
