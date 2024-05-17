<?php

namespace App\Controller;

use App\Entity\Transporte;
use App\Entity\Cliente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CarroController extends AbstractController
{
    #[Route('/transporte/cadastrar', name: 'transporte_cadastrar', methods: ['POST'])]
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

        // Cria uma nova instância de Transporte
        $transporte = new Transporte();
        $transporte->setTipo($data['tipo']);
        $transporte->setPlaca($data['placa']);
        $transporte->setCliente($cliente);

        // Persiste os dados no banco de dados
        $em->persist($transporte);
        $em->flush();

        return new JsonResponse([
            'status' => '201 - Created',
            'mensagemUsuario' => 'Transporte cadastrado com sucesso.',
            'id' => $transporte->getId()
        ], Response::HTTP_CREATED);
    }

    #[Route('/transporte/atualizar/{cpfCliente}', name: 'transporte_atualizar', methods: ['PUT'])]
    public function atualizar($cpfCliente, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Busca o cliente pelo CPF
        $cliente = $em->getRepository(Cliente::class)->findOneBy(['cpfCliente' => $cpfCliente]);
        if (!$cliente) {
            return new JsonResponse([
                'status' => '400 - Bad Request',
                'mensagemUsuario' => 'O CPF informado não está cadastrado.',
                'mensagemTecnica' => 'Não foi possível encontrar um cliente com o CPF fornecido.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Busca o transporte pelo cliente
        $transporte = $em->getRepository(Transporte::class)->findOneBy(['cliente' => $cliente]);
        if (!$transporte) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Transporte não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Atualiza os dados do transporte com os dados fornecidos na requisição
        $transporte->setTipo($data['tipo']);
        $transporte->setPlaca($data['placa']);
        $transporte->setCliente($cliente);

        // Persiste as alterações no banco de dados
        $em->flush();

        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Transporte atualizado com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/transporte/excluir/{cpfCliente}', name: 'transporte_excluir', methods: ['DELETE'])]
    public function excluir($cpfCliente, EntityManagerInterface $em): JsonResponse
    {
        // Busca o cliente pelo CPF
        $cliente = $em->getRepository(Cliente::class)->findOneBy(['cpfCliente' => $cpfCliente]);
        if (!$cliente) {
            return new JsonResponse([
                'status' => '400 - Bad Request',
                'mensagemUsuario' => 'O CPF informado não está cadastrado.',
                'mensagemTecnica' => 'Não foi possível encontrar um cliente com o CPF fornecido.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Busca o transporte pelo cliente
        $transporte = $em->getRepository(Transporte::class)->findOneBy(['cliente' => $cliente]);
        if (!$transporte) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Transporte não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Remove o transporte do banco de dados
        $em->remove($transporte);
        $em->flush();

        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Transporte excluído com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/transporte/consultar/{cpfCliente}', name: 'transporte_consultar', methods: ['GET'])]
    public function consultar($cpfCliente, EntityManagerInterface $em): JsonResponse
    {
        // Busca o cliente pelo CPF
        $cliente = $em->getRepository(Cliente::class)->findOneBy(['cpfCliente' => $cpfCliente]);
        if (!$cliente) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Cliente não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Busca o transporte pelo cliente
        $transportes = $em->getRepository(Transporte::class)->findBy(['cliente' => $cliente]);
        if (!$transportes) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Transporte(s) não encontrado(s).'
            ], Response::HTTP_NOT_FOUND);
        }

        $response = [];
        foreach ($transportes as $transporte) {
            $response[] = [
                'id' => $transporte->getId(),
                'tipo' => $transporte->getTipo(),
                'placa' => $transporte->getPlaca(),
                'cpfCliente' => $transporte->getCliente()->getCpfCliente()
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/transporte/consultar', name: 'transporte_consultar_todos', methods: ['GET'])]
    public function consultarTodos(EntityManagerInterface $em): JsonResponse
    {
        // Busca todos os transportes
        $transportes = $em->getRepository(Transporte::class)->findAll();

        if (!$transportes) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Nenhum transporte encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        $response = [];
        foreach ($transportes as $transporte) {
            $response[] = [
                'id' => $transporte->getId(),
                'tipo' => $transporte->getTipo(),
                'placa' => $transporte->getPlaca(),
                'cpfCliente' => $transporte->getCliente()->getCpfCliente()
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
