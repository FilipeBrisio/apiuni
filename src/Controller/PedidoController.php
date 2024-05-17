<?php

namespace App\Controller;

use App\Entity\Pedidos;
use App\Entity\Cliente;
use App\Entity\Produtos;
use App\Entity\Transporte;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class PedidoController extends AbstractController
{
    #[Route('/pedido/cadastrar', name: 'pedido_cadastrar', methods: ['POST'])]
    public function cadastrar(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Verifica se o CPF do cliente existe
        $cliente = $em->getRepository(Cliente::class)->findOneBy(['cpfCliente' => $data['cpfCliente']]);
        if (!$cliente) {
            return new JsonResponse([
                'status' => '400 - Bad Request',
                'mensagemUsuario' => 'O CPF informado não está cadastrado.',
                'mensagemTecnica' => 'Não foi possível encontrar um cliente com o CPF fornecido.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verifica se a placa do carro pertence ao cliente
        $placaCarro = $em->getRepository(Transporte::class)->findOneBy(['placa' => $data['placaCarro'], 'cliente' => $cliente]);
        if (!$placaCarro) {
            return new JsonResponse([
                'status' => '400 - Bad Request',
                'mensagemUsuario' => 'A placa do carro informada não pertence ao cliente.',
                'mensagemTecnica' => 'Não foi possível encontrar um carro com a placa informada associada ao cliente.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verifica se os produtos existem e calcula o total do pedido
        $total = 0;
        foreach ($data['produtosPedido'] as $item) {
            $produto = $em->getRepository(Produtos::class)->find($item['id']);
            if (!$produto) {
                return new JsonResponse([
                    'status' => '400 - Bad Request',
                    'mensagemUsuario' => 'Um dos produtos informados não existe.',
                    'mensagemTecnica' => 'Não foi possível encontrar um produto com o ID fornecido.'
                ], Response::HTTP_BAD_REQUEST);
            }
            $total += $produto->getPreco() * $item['quantidade'];
        }

        // Se a forma de pagamento for "Credito_Pessoal", verifique e abata o crédito do cliente
        if ($data['forma_de_pagamento'] === 'Credito_Pessoal') {
            // Verifica se o cliente possui crédito suficiente
            if ($cliente->getCredito() < $total) {
                return new JsonResponse([
                    'status' => '400 - Bad Request',
                    'mensagemUsuario' => 'Crédito insuficiente.',
                    'mensagemTecnica' => 'O cliente não possui crédito suficiente para efetuar o pagamento.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Abate o valor do pedido do crédito do cliente
            $cliente->setCredito($cliente->getCredito() - $total);
            $em->persist($cliente);
        }

        // Cria uma nova instância de Pedido
        $pedido = new Pedidos();
        $pedido->setCpfCliente($cliente);
        $pedido->setPlacaCarro($placaCarro);
        $pedido->setFormaDePagamento($data['forma_de_pagamento']);
        $pedido->setTotal($total);
        $pedido->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $em->persist($pedido);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/pedido/atualizar/{id}', name: 'pedido_atualizar', methods: ['PUT'])]
    public function atualizar($id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Busca o pedido pelo ID
        $pedido = $em->getRepository(Pedidos::class)->find($id);

        // Se o pedido não for encontrado, retorna 404
        if (!$pedido) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Pedido não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Verifica se o CPF do cliente existe
        if (isset($data['cpfCliente'])) {
            $cliente = $em->getRepository(Cliente::class)->findOneBy(['cpfCliente' => $data['cpfCliente']]);
            if (!$cliente) {
                return new JsonResponse([
                    'status' => '400 - Bad Request',
                    'mensagemUsuario' => 'O CPF informado não está cadastrado.',
                    'mensagemTecnica' => 'Não foi possível encontrar um cliente com o CPF fornecido.'
                ], Response::HTTP_BAD_REQUEST);
            }
            $pedido->setCpfCliente($cliente);
        }

        // Verifica se a placa do carro pertence ao cliente
        if (isset($data['placaCarro'])) {
            $placaCarro = $em->getRepository(Transporte::class)->findOneBy(['placa' => $data['placaCarro'], 'cliente' => $pedido->getCpfCliente()]);
            if (!$placaCarro) {
                return new JsonResponse([
                    'status' => '400 - Bad Request',
                    'mensagemUsuario' => 'A placa do carro informada não pertence ao cliente.',
                    'mensagemTecnica' => 'Não foi possível encontrar um carro com a placa informada associada ao cliente.'
                ], Response::HTTP_BAD_REQUEST);
            }
            $pedido->setPlacaCarro($placaCarro);
        }

        // Verifica se os produtos existem e calcula o total do pedido
        if (isset($data['produtosPedido'])) {
            $total = 0;
            foreach ($data['produtosPedido'] as $item) {
                $produto = $em->getRepository(Produtos::class)->find($item['id']);
                if (!$produto) {
                    return new JsonResponse([
                        'status' => '400 - Bad Request',
                        'mensagemUsuario' => 'Um dos produtos informados não existe.',
                        'mensagemTecnica' => 'Não foi possível encontrar um produto com o ID fornecido.'
                    ], Response::HTTP_BAD_REQUEST);
                }
                $total += $produto->getPreco() * $item['quantidade'];
            }
            $pedido->setTotal($total);
        }

        // Verifica e atualiza a forma de pagamento
        if (isset($data['forma_de_pagamento'])) {
            $pedido->setFormaDePagamento($data['forma_de_pagamento']);
        }

        // Persiste as alterações no banco de dados
        $em->flush();

        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Pedido atualizado com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/pedido/excluir/{id}', name: 'pedido_excluir', methods: ['DELETE'])]
    public function excluir($id, EntityManagerInterface $em): JsonResponse
    {
        // Busca o pedido pelo ID
        $pedido = $em->getRepository(Pedidos::class)->find($id);

        // Se o pedido não for encontrado, retorna 404
        if (!$pedido) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Pedido não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Remove o pedido do banco de dados
        $em->remove($pedido);
        $em->flush();

        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Pedido excluído com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/pedido/consultar/{id}', name: 'pedido_consultar', methods: ['GET'])]
    public function consultar($id, EntityManagerInterface $em): JsonResponse
    {
        // Busca o pedido pelo ID
        $pedido = $em->getRepository(Pedidos::class)->find($id);

        // Se o pedido não for encontrado, retorna 404
        if (!$pedido) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Pedido não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Retorna os dados do pedido encontrado
        $response = [
            'id' => $pedido->getId(),
            'cpfCliente' => $pedido->getCpfCliente()->getCpfCliente(),
            'placaCarro' => $pedido->getPlacaCarro()->getPlaca(),
            'forma_de_pagamento' => $pedido->getFormaDePagamento(),
            'total' => $pedido->getTotal(),
            'createdAt' => $pedido->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/pedido/listar', name: 'pedido_listar', methods: ['GET'])]
    public function listar(EntityManagerInterface $em): JsonResponse
    {
        // Busca todos os pedidos
        $pedidos = $em->getRepository(Pedidos::class)->findAll();

        // Se não houver pedidos, retorna 404
        if (!$pedidos) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        // Retorna os dados de todos os pedidos
        $response = [];
        foreach ($pedidos as $pedido) {
            $response[] = [
                'id' => $pedido->getId(),
                'cpfCliente' => $pedido->getCpfCliente()->getCpfCliente(),
                'placaCarro' => $pedido->getPlacaCarro()->getPlaca(),
                'forma_de_pagamento' => $pedido->getFormaDePagamento(),
                'total' => $pedido->getTotal(),
                'createdAt' => $pedido->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    
}
