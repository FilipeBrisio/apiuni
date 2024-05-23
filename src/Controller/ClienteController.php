<?php

namespace App\Controller;

use App\Entity\Cliente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClienteController extends AbstractController
{

    #[Route('/cliente/cadastrar', name: 'cliente_cadastrar_form', methods: ['GET'])]
    public function mostrarFormularioCadastro(): Response
    {
        return $this->render('clientes/cadastrar_cliente.html.twig');
    }
    
    #[Route('/cliente/cadastrar', name: 'cliente_cadastrar', methods: ['POST'])]
    public function cadastrar(Request $request, EntityManagerInterface $em): Response
    {
        // Obtém os dados do formulário
        $data = $request->request->all();

        // Verifica se os dados necessários estão presentes no array $data
        if (!isset($data['nome']) || !isset($data['telefone']) || !isset($data['cpf'])) {
            // Se algum dado estiver faltando, redireciona de volta para o formulário com uma mensagem de erro
            return $this->redirectToRoute('cliente_cadastrar_form', ['error' => 'Por favor, preencha todos os campos corretamente.']);
        }

        // Cria uma nova instância de Cliente
        $cliente = new Cliente();
        $cliente->setNomeCliente($data['nome']);
        $cliente->setTelefoneCliente($data['telefone']);
        $cliente->setCpfCliente($data['cpf']);
        $cliente->setCredito(5000);
        $cliente->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $cliente->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        // Persiste os dados no banco de dados
        $em->persist($cliente);
        $em->flush();

        // Redireciona para uma página de sucesso
        return $this->redirectToRoute('cliente_consultar');
    }

    

    #[Route('/cliente/atualizar/{id}', name: 'cliente_atualizar', methods: ['PUT'])]
    public function atualizar($id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Busca o cliente pelo ID
        $cliente = $em->getRepository(Cliente::class)->find($id);
    
        // Se o cliente não for encontrado, retorna 404
        if (!$cliente) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Cliente não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }
    
        // Atualiza os dados do cliente com os dados fornecidos na requisição, se existirem
        $data = json_decode($request->getContent(), true);
        if (isset($data['nomeCliente'])) {
            $cliente->setNomeCliente($data['nomeCliente']);
        }
        if (isset($data['telefoneCliente'])) {
            $cliente->setTelefoneCliente($data['telefoneCliente']);
        }
        if (isset($data['cpfCliente'])) {
            // Verifica se o novo CPF já está associado a outro cliente
            $existingCliente = $em->getRepository(Cliente::class)->findOneBy(['cpfCliente' => $data['cpfCliente']]);
            if ($existingCliente && $existingCliente->getId() !== $cliente->getId()) {
                return new JsonResponse([
                    'status' => '400 - Bad Request',
                    'mensagemUsuario' => 'O CPF informado já está cadastrado para outro cliente.',
                    'mensagemTecnica' => 'Não é possível atualizar o CPF para este valor, pois já está associado a outro cliente.'
                ], Response::HTTP_BAD_REQUEST);
            }
            $cliente->setCpfCliente($data['cpfCliente']);
        }
    
        // Atualiza a data de atualização
        $cliente->setUpdatedAt(new \DateTimeImmutable());
    
        // Persiste as alterações no banco de dados
        $em->flush();
    
        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Cliente atualizado com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/cliente/excluir/{id}', name: 'cliente_excluir', methods: ['DELETE'])]
    public function excluir($id, EntityManagerInterface $em): JsonResponse
    {
        // Busca o cliente pelo ID
        $cliente = $em->getRepository(Cliente::class)->find($id);

        // Se o cliente não for encontrado, retorna 404
        if (!$cliente) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Cliente não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Remove o cliente do banco de dados
        $em->remove($cliente);
        $em->flush();

        return new JsonResponse([
            'status' => '204 - No Content',
            'mensagemUsuario' => 'Cliente excluído com sucesso.'
        ], Response::HTTP_NO_CONTENT);
    }

    #[Route('/cliente/consultar/{id}', name: 'cliente_consultar_id', methods: ['GET'])]
    public function consultarPorId($id, EntityManagerInterface $em): JsonResponse
    {
        // Busca o cliente pelo ID
        $cliente = $em->getRepository(Cliente::class)->find($id);

        // Se o cliente não for encontrado, retorna 404
        if (!$cliente) {
            return new JsonResponse([
                'status' => '404 - Not Found',
                'mensagemUsuario' => 'Cliente não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Retorna os dados do cliente encontrado
        $response = [
            'id' => $cliente->getId(),
            'nomeCliente' => $cliente->getNomeCliente(),
            'telefoneCliente' => $cliente->getTelefoneCliente(),
            'cpfCliente' => $cliente->getCpfCliente(),
            'credito' => $cliente->getCredito(),
            'createdAt' => $cliente->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $cliente->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/cliente/consultar', name: 'cliente_consultar', methods: ['GET'])]
    public function consultar(EntityManagerInterface $em): JsonResponse
    {
        // Busca todos os clientes
        $clientes = $em->getRepository(Cliente::class)->findAll();

        // Se não houver clientes, retorna 404
        if (!$clientes) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        // Retorna os dados de todos os clientes
        $response = [];
        foreach ($clientes as $cliente) {
            $response[] = [
                'id' => $cliente->getId(),
                'nomeCliente' => $cliente->getNomeCliente(),
                'telefoneCliente' => $cliente->getTelefoneCliente(),
                'cpfCliente' => $cliente->getCpfCliente(),
                'credito' => $cliente->getCredito(),
                'createdAt' => $cliente->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $cliente->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
