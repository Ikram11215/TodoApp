<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFiltersType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, Request $request): Response
    {
        $form = $this->createForm(TaskFiltersType::class);
        $form->handleRequest($request);

        $filters = $form->isSubmitted() && $form->isValid() ? $form->getData() : [];

        $user = $this->getUser();
        $tasks = $user ? $taskRepository->findBy(['user' => $user]) : [];

        // Récupération des tâches temporaires de la session
        $temporaryTasks = $request->getSession()->get('temporary_tasks', []);
        $tasks = array_merge($tasks, $temporaryTasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'filters' => array_merge($filters, ['form' => $form->createView()]),
        ]);
    }

    #[Route('/task/{id}/transition/{transition}', name: 'app_task_transition', methods: ['POST'])]
    public function transition(Task $task, string $transition, WorkflowInterface $taskWorkflow, EntityManagerInterface $entityManager): Response
    {
        if ($taskWorkflow->can($task, $transition)) {
            $taskWorkflow->apply($task, $transition);
            $entityManager->flush();
            $this->addFlash('success', sprintf('Transition "%s" appliquée avec succès.', $transition));
        } else {
            $this->addFlash('error', sprintf('La transition "%s" ne peut pas être appliquée.', $transition));
        }

        return $this->redirectToRoute('app_task');
    }

    #[Route('/task/add', name: 'app_task_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $name = $request->request->get('name');
        $priority = $request->request->getInt('priority');

        if ($name && $priority) {
            $task = new Task();
            $task->setName($name);
            $task->setPriority($priority);
            $task->setState('pending');
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setUser($this->getUser());

            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash('success', 'Tâche ajoutée avec succès.');
        } else {
            $session = $request->getSession();
            $tasks = $session->get('temporary_tasks', []);
            $tasks[] = [
                'id' => uniqid(),
                'name' => $name,
                'priority' => $priority,
                'state' => 'pending',
                'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
            $session->set('temporary_tasks', $tasks);
            $this->addFlash('info', 'Tâche ajoutée temporairement.');
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
