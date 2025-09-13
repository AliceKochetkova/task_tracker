<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = [
            ['name' => 'ROLE_ADMIN', 'slug' => 'admin'],
            ['name' => 'ROLE_MANAGER', 'slug' => 'manager'],
            ['name' => 'ROLE_USER', 'slug' => 'user'],
        ];

        foreach ($roles as $data) {
            $role = new Role();
            $role->setName($data['name']);
            $role->setSlug($data['slug']);
            $role->setIsActive(true);
            $role->setCreatedAt(new \DateTimeImmutable());
            $role->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($role);
            echo "Persisted role: {$data['name']}\n";
        }

        $manager->flush();
    }
}
