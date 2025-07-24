<?php

namespace App\DataFixtures;

use App\Entity\Restaurante;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RestauranteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            ['nombre' => 'La Parrilla', 'direccion' => 'Calle Falsa 123', 'telefono' => '123456789'],
            ['nombre' => 'Pizza Nova', 'direccion' => 'Av. Central 456', 'telefono' => '987654321'],
            ['nombre' => 'Sushi Go', 'direccion' => 'Boulevard 789', 'telefono' => '+34 600 123 456'],
        ];
        foreach ($data as $item) {
            $r = new Restaurante();
            $r->setNombre($item['nombre']);
            $r->setDireccion($item['direccion']);
            $r->setTelefono($item['telefono']);
            $manager->persist($r);
        }
        $manager->flush();
    }
} 