<?php


namespace App\Controller\Api;

use App\Entity\Project;
use App\Enum\ProjectStatus;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/projects')]
class ProjectController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, ProjectRepository $repository): JsonResponse
    {
        $status = $request->query->get('status'); // фильтрация
        $criteria = [];

        if ($status) {
            $criteria['status'] = $status;
        }

        $projects = $repository->findBy($criteria);

        return $this->json($projects, 200, [], ['groups' => ['project:read']]);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_MANAGER')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $project = new Project();
        $project->setName($data['name'] ?? 'Без названия');
        $project->setDescription($data['description'] ?? null);
        $project->setStatus(ProjectStatus::from($data['status']));
        $project->setCreatedBy($this->getUser() ?? null);


        $em->persist($project);
        $em->flush();

        return $this->json($project, 201, [], ['groups' => ['project:read']]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Project $project): JsonResponse
    {
        return $this->json($project, 200, [], ['groups' => ['project:read']]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Project $project, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($project->getCreatedBy() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) {
            $project->setName($data['name']);
        }
        if (isset($data['description'])) {
            $project->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $project->setStatus(ProjectStatus::from($data['status']));
        }
        $em->flush();

        return $this->json($project, 200, [], ['groups' => ['project:read']]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Project $project, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($project->getCreatedBy() !== $user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        $em->remove($project);
        $em->flush();

        return $this->json(null, 204);
    }
}

