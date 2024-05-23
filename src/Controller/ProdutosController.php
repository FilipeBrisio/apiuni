<?php

namespace App\Controller;

use App\Entity\Produtos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProdutosController extends AbstractController
{
    #[Route('/produtos/cadastrar', name: 'produtos_cadastrar', methods: ['POST'])]
    public function cadastrar(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Cria uma nova instância de Produto
        $produto = new Produtos();
        $produto->setNome($data['nome']);
        $produto->setPreco($data['preco']);
        $produto->setQuantidade($data['quantidade']);
        $produto->setTipo($data['tipo']);
        $produto->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $produto->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        // Persiste os dados no banco de dados
        $em->persist($produto);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/produtos/atualizar/{id}', name: 'produtos_atualizar', methods: ['PUT'])]
    public function atualizar($id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Busca o produto pelo ID
        $produto = $em->getRepository(Produtos::class)->find($id);

        // Se o produto não for encontrado, retorna 404
        if (!$produto) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Produto não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Atualiza os dados do produto com os dados fornecidos na requisição
        $data = json_decode($request->getContent(), true);
        $produto->setNome($data['nome']);
        $produto->setPreco($data['preco']);
        $produto->setQuantidade($data['quantidade']);
        $produto->setTipo($data['tipo']);
        $produto->setUpdatedAt(new \DateTimeImmutable());

        // Persiste as alterações no banco de dados
        $em->flush();

        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Produto atualizado com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/produtos/excluir/{id}', name: 'produtos_excluir', methods: ['DELETE'])]
    public function excluir($id, EntityManagerInterface $em): JsonResponse
    {
        // Busca o produto pelo ID
        $produto = $em->getRepository(Produtos::class)->find($id);

        // Se o produto não for encontrado, retorna 404
        if (!$produto) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Produto não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Remove o produto do banco de dados
        $em->remove($produto);
        $em->flush();

        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Produto excluído com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/produtos/consultar', name: 'produtos_consultar', methods: ['GET'])]
public function consultarTodos(EntityManagerInterface $em): JsonResponse
{
    // Busca todos os produtos
    $produtos = $em->getRepository(Produtos::class)->findAll();

    // Se não houver produtos, retorna 404
    if (!$produtos) {
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
    
    // Retorna os dados de todos os produtos com createdAt e updatedAt
    $response = [];
    foreach ($produtos as $produto) {
        $produtosPedidos = [];
        foreach ($produto->getPedidos() as $pedido) {
            // Obtém o produto associado ao pedido
            $produtoDoPedido = $pedido->getProduto();

            // Adiciona os dados do pedido ao array de pedidos
            $produtosPedidos[] = [
                'id' => $pedido->getId(),
                'cpfCliente' => $pedido->getCpfCliente()->getCpfCliente(),
                'placaCarro' => $pedido->getPlacaCarro()->getPlaca(),
                'forma_de_pagamento' => $pedido->getFormaDePagamento(),
                'nome_produto' => $produtoDoPedido->getNome(),
                'tipo_produto' => $produtoDoPedido->getTipo(),
                'total' => $pedido->getTotal(),
                'createdAt' => $pedido->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }
        $response[] = [
            'id' => $produto->getId(),
            'nome' => $produto->getNome(),
            'preco' => $produto->getPreco(),
            'quantidade' => $produto->getQuantidade(),
            'tipo' => $produto->getTipo(),
            'createdAt' => $produto->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $produto->getUpdatedAt()->format('Y-m-d H:i:s'),
            'pedidos' => $produtosPedidos
        ];
      
    }

    return new JsonResponse($response, Response::HTTP_OK);
}
    

    #[Route('/produtos/consultar/{id}', name: 'produtos_consultar_por_id', methods: ['GET'])]
    public function consultarPorId($id, EntityManagerInterface $em): JsonResponse
    {
        // Busca o produto pelo ID
        $produto = $em->getRepository(Produtos::class)->find($id);

        // Se o produto não for encontrado, retorna 404
        if (!$produto) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Produto não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Retorna os dados do produto com createdAt e updatedAt
        $response = [
            'id' => $produto->getId(),
            'nome' => $produto->getNome(),
            'preco' => $produto->getPreco(),
            'quantidade' => $produto->getQuantidade(),
            'tipo' => $produto->getTipo(),
            'createdAt' => $produto->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $produto->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
    
}
