<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(Request $request,TaskRepository $repository): JsonResponse
    {
        $status = $request->query->get('status');
        $sort = $request->query->get('sort', 'createdAt');
        $direction = $request->query->get('direction', 'DESC');

        $criteria = [];
        if ($status) {
            $criteria['status'] = $status;
        }

        $tasks = $repository->findBy($criteria, [$sort, $direction]);

        return $this->json($tasks, 200, [], ['groups' => ['task:read']]);
    }

    #[Route('', methods: ['POST'])]
    //#[IsGranted('ROLE_MANAGER')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($data['title'] ?? 'Без названия');
        $task->setDescription($data['description'] ?? null);
        $task->setStatus(TaskStatus::from($data['status']));
        $task->setPriority($data['priority'] ?? 'low');

        if (!empty($data['dueDate'])) {
            $task->setDueDate(new \DateTimeImmutable($data['dueDate']));
        }
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTimeImmutable());
        $task->setCreatedBy($this->getUser());


        $em->persist($task);
        $em->flush();

        return $this->json($task, 201, [], ['groups' => ['task:read']]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function details(Task $task): JsonResponse
    {
        return $this->json($task, 200, [], ['groups' => ['task:read']]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Task $task, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($task->getCreatedBy() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['tittle'])) {
            $task->setTitle($data['tittle']);
        }
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $task->setStatus(TaskStatus::from($data['status']));
        }
        if (isset($data['priority'])) {
            $task->setPriority($data['priority']);
        }
        if (!empty($data['dueDate'])) {
            $task->setDueDate(new \DateTimeImmutable($data['dueDate']));
        }

        $task->setUpdatedAt(new \DateTimeImmutable());

        $em->flush();

        return $this->json($task, 200, [], ['groups' => ['task:read']]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($task->getCreatedBy() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        $em->remove($task);
        $em->flush();

        return $this->json(null, 204);
    }
}
