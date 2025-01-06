<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, Request $request): Response
    {
        $name = $request->query->get('name');
        $priority = $request->query->get('priority');
        $state = $request->query->get('state');

        $priority = $priority !== '' ? (int) $priority : null;
        $state = $state !== '' ? $state : null;

        $tasks = $taskRepository->findByFilters($name, $priority, $state);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'filters' => [
                'name' => $name,
                'priority' => $priority,
                'state' => $state,
            ],
        ]);
    }

    #[Route('/task/add', name: 'app_task_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $name = $request->request->get('name');
        $priority = $request->request->get('priority');

        if ($name && $priority) {
            $task = new Task();
            $task->setName($name);
            $task->setPriority((int) $priority);
            $task->setState('pending');
            $task->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche ajoutée avec succès.');
        }

        return $this->redirectToRoute('app_task');
    }

    #[Route('/task/{id}/complete', name: 'app_task_complete', methods: ['POST'])]
    public function complete(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->setState('completed');
        $entityManager->flush();

        $this->addFlash('success', 'Tâche marquée comme terminée.');

        return $this->redirectToRoute('app_task');
    }

    #[Route('/task/{id}/delete', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'Tâche supprimée avec succès.');

        return $this->redirectToRoute('app_task');
    }
}
