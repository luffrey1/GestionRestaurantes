<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class RestauranteSchema
{
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
    public function schemas() {}
} 