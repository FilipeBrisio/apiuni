<?php

namespace App\Entity;

use App\Repository\CaixaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaixaRepository::class)]
class Caixa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $dadosCaixa = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDadosCaixa(): array
    {
        return $this->dadosCaixa;
    }

    public function setDadosCaixa(array $dadosCaixa): static
    {
        $this->dadosCaixa = $dadosCaixa;

        return $this;
    }
}
