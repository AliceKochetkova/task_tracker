<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'api_users_list', methods: ['GET'])]
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER'); // только admin и manager

        $users = $this->userRepository->findAll();
        $data = $this->serializer->serialize($users, 'json', ['groups' => ['user:read']]);

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'api_users_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser || !in_array('ROLE_MANAGER', $currentUser->getRoles()) && $currentUser !== $user) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = $this->serializer->serialize($user, 'json', ['groups' => ['user:read']]);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'api_users_update', methods: ['PUT'])]
    public function update(User $user, Request $request): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser || ($currentUser !== $user && !in_array('ROLE_ADMIN', $currentUser->getRoles()))) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) $user->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $user->setLastName($data['lastName']);
        if (isset($data['email'])) $user->setEmail($data['email']);
        if (isset($data['phone'])) $user->setPhone($data['phone']);

        $user->setUpdatedAt(new \DateTimeImmutable());

        $this->em->flush();

        $json = $this->serializer->serialize($user, 'json', ['groups' => ['user:read']]);

        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
