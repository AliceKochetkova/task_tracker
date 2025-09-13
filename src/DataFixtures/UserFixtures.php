<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use App\Enum\UserStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public static function getGroups(): array
    {
        return ['users'];
    }
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roleAdmin = $manager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']);
        $roleUser = $manager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_USER']);
        $roleManager = $manager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_MANAGER']);

        if (!$roleAdmin || !$roleUser || !$roleManager) {
            throw new \Exception('Roles must exist in the database before loading users.');
        }

        $usersData = [
            [
                'firstName' => 'Admin',
                'lastName' => 'User',
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'role' => $roleAdmin
            ],
            [
                'firstName' => 'Manager',
                'lastName' => 'User1',
                'email' => 'manager@example.com',
                'password' => 'manager123',
                'role' => $roleManager
            ],
            [
                'firstName' => 'Regular',
                'lastName' => 'User2',
                'email' => 'user@example.com',
                'password' => 'user123',
                'role' => $roleUser
            ],
        ];

        foreach ($usersData as $data) {
            $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                continue; // Пропускаем, если пользователь уже есть
            }

            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $user->setRole($data['role']);
            $user->setStatus(UserStatus::ACTIVE);

            // Хешируем пароль
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
