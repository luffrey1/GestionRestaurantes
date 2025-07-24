<?php

namespace App\Entity;

use App\Repository\RestauranteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="Restaurante",
 *   required={"id", "nombre", "direccion", "telefono"},
 *   @OA\Property(property="id", type="integer", example=1, description="ID único del restaurante"),
 *   @OA\Property(property="nombre", type="string", example="La Parrilla", description="Nombre del restaurante"),
 *   @OA\Property(property="direccion", type="string", example="Calle Falsa 123", description="Dirección física"),
 *   @OA\Property(property="telefono", type="string", example="+34 600 123 456", description="Teléfono de contacto")
 * )
 *
 * @OA\Schema(
 *   schema="RestauranteCreate",
 *   required={"nombre", "direccion", "telefono"},
 *   @OA\Property(property="nombre", type="string", example="La Parrilla", description="Nombre del restaurante"),
 *   @OA\Property(property="direccion", type="string", example="Calle Falsa 123", description="Dirección física"),
 *   @OA\Property(property="telefono", type="string", example="+34 600 123 456", description="Teléfono de contacto")
 * )
 */
#[ORM\Entity(repositoryClass: RestauranteRepository::class)]
class Restaurante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private string $nombre;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    private string $direccion;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 7, max: 20)]
    #[Assert\Regex(pattern: '/^[0-9\-\+\s]+$/', message: 'El teléfono solo puede contener números, espacios, guiones o +')]
    private string $telefono;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }
    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }
    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;
        return $this;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }
    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;
        return $this;
    }
} 