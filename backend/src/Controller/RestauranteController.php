<?php

namespace App\Controller;

use App\Entity\Restaurante;
use App\Repository\RestauranteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

/**
 * Controlador para la gestión de restaurantes.
 * 
 * @OA\Tag(name="Restaurantes", description="Operaciones CRUD sobre restaurantes")
 */
#[Route('/api/restaurantes')]
class RestauranteController extends AbstractController
{
    private RestauranteRepository $repo;
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(RestauranteRepository $repo, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->repo = $repo;
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * Lista todos los restaurantes.
     *
     * Recupera todos los restaurantes registrados en el sistema.
     *
     * @OA\Get(
     *     path="/api/restaurantes",
     *     summary="Listar restaurantes",
     *     tags={"Restaurantes"},
     *     security={{"api_key": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de restaurantes",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Restaurante"))
     *     ),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // Asegura que la carpeta de logs existe
        $logDir = __DIR__ . '/../../var/log';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents($logDir . '/custom.log', 'Acceso a index: ' . date('c') . PHP_EOL, FILE_APPEND);

        $restaurantes = $this->repo->findAll();
        $data = array_map(fn($r) => [
            'id' => $r->getId(),
            'nombre' => $r->getNombre(),
            'direccion' => $r->getDireccion(),
            'telefono' => $r->getTelefono(),
        ], $restaurantes);

        file_put_contents($logDir . '/custom.log', 'Data: ' . print_r($data, true) . PHP_EOL, FILE_APPEND);

        return $this->json($data);
    }

    /**
     * Obtiene un restaurante por ID.
     *
     * @OA\Get(
     *     path="/api/restaurantes/{id}",
     *     summary="Obtener restaurante por ID",
     *     tags={"Restaurantes"},
     *     security={{"api_key": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del restaurante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restaurante encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Restaurante")
     *     ),
     *     @OA\Response(response=404, description="Restaurante no encontrado"),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $r = $this->repo->find($id);
        if (!$r) {
            return $this->json(['message' => 'Restaurante no encontrado'], 404);
        }
        $data = [
            'id' => $r->getId(),
            'nombre' => $r->getNombre(),
            'direccion' => $r->getDireccion(),
            'telefono' => $r->getTelefono(),
        ];
        return $this->json($data);
    }

    /**
     * Crea un nuevo restaurante.
     *
     * @OA\Post(
     *     path="/api/restaurantes",
     *     summary="Crear restaurante",
     *     tags={"Restaurantes"},
     *     security={{"api_key": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RestauranteCreate")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Restaurante creado",
     *         @OA\JsonContent(ref="#/components/schemas/Restaurante")
     *     ),
     *     @OA\Response(response=400, description="Datos inválidos"),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $restaurante = new Restaurante();
        $restaurante->setNombre($data['nombre'] ?? '');
        $restaurante->setDireccion($data['direccion'] ?? '');
        $restaurante->setTelefono($data['telefono'] ?? '');

        $errors = $this->validator->validate($restaurante);
        if (count($errors) > 0) {
            return $this->json(['message' => (string) $errors], 400);
        }

        $this->em->persist($restaurante);
        $this->em->flush();
        $data = [
            'id' => $restaurante->getId(),
            'nombre' => $restaurante->getNombre(),
            'direccion' => $restaurante->getDireccion(),
            'telefono' => $restaurante->getTelefono(),
        ];
        return $this->json($data, 201);
    }

    /**
     * Actualiza un restaurante existente.
     *
     * @OA\Put(
     *     path="/api/restaurantes/{id}",
     *     summary="Actualizar restaurante",
     *     tags={"Restaurantes"},
     *     security={{"api_key": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del restaurante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RestauranteCreate")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restaurante actualizado",
     *         @OA\JsonContent(ref="#/components/schemas/Restaurante")
     *     ),
     *     @OA\Response(response=400, description="Datos inválidos"),
     *     @OA\Response(response=404, description="Restaurante no encontrado"),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $restaurante = $this->repo->find($id);
        if (!$restaurante) {
            return $this->json(['message' => 'Restaurante no encontrado'], 404);
        }
        $data = json_decode($request->getContent(), true);
        $restaurante->setNombre($data['nombre'] ?? $restaurante->getNombre());
        $restaurante->setDireccion($data['direccion'] ?? $restaurante->getDireccion());
        $restaurante->setTelefono($data['telefono'] ?? $restaurante->getTelefono());

        $errors = $this->validator->validate($restaurante);
        if (count($errors) > 0) {
            return $this->json(['message' => (string) $errors], 400);
        }

        $this->em->flush();
        $data = [
            'id' => $restaurante->getId(),
            'nombre' => $restaurante->getNombre(),
            'direccion' => $restaurante->getDireccion(),
            'telefono' => $restaurante->getTelefono(),
        ];
        return $this->json($data);
    }

    /**
     * Elimina un restaurante.
     *
     * @OA\Delete(
     *     path="/api/restaurantes/{id}",
     *     summary="Eliminar restaurante",
     *     tags={"Restaurantes"},
     *     security={{"api_key": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del restaurante",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Restaurante eliminado"),
     *     @OA\Response(response=404, description="Restaurante no encontrado"),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $restaurante = $this->repo->find($id);
        if (!$restaurante) {
            return $this->json(['message' => 'Restaurante no encontrado'], 404);
        }
        $this->em->remove($restaurante);
        $this->em->flush();
        return $this->json(null, 204);
    }
} 