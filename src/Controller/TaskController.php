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

        $user = $this->getUser(); // Get the currently logged-in user
        $id = null;
        $name = null;
        $priority = null;
        $state = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $id = $data['id'];
            $name = $data['name'];
            $priority = $data['priority'];
            $state = $data['state'];
        }

        if(!$user){
            $tasks = $taskRepository->findBy(['user' => null]);
        } else {
            $tasks = $user ? $taskRepository->findBy(criteria: ['user' => $user]) : [];
        }
        // Retrieve tasks for the logged-in user
            
        // Retrieve temporary tasks from the session
        $session = $request->getSession();
        $temporaryTasks = $session->get('temporary_tasks', []);
        $tasks = array_merge($tasks, $temporaryTasks);



        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'filters' => [
                'id' => $id,
                'name' => $name,
                'priority' => $priority,
                'state' => $state,
                'form' => $form->createView(),
            ],
        ]);
    }

    #[Route('/task/{id}/transition/{transition}', name: 'app_task_transition', methods: ['POST'])]
    public function transition(
        Task $task,
        string $transition,
        WorkflowInterface $taskWorkflow,
        EntityManagerInterface $entityManager
    ): Response {
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
            $taskEntity = new Task();
            $taskEntity->setName($name);
            $taskEntity->setPriority((int) $priority);
            $taskEntity->setState('pending');
            $taskEntity->setCreatedAt(new \DateTimeImmutable());
            $taskEntity->setUser($this->getUser()); // Associate the task with the logged-in user

            $entityManager->persist($taskEntity);
            $entityManager->flush();
            $this->addFlash('success', 'Tâche ajoutée avec succès.');
        } else {
            // Store the task in the session
            $session = $request->getSession();
            $tasks = $session->get('temporary_tasks', []);
            $tasks[] = [
                'id' => uniqid(),
                'name' => $name,
                'priority' => (int) $priority,
                'state' => 'pending',
                'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
            $session->set('temporary_tasks', $tasks);
            $this->addFlash('info', 'Tâche ajoutée, mais ne sera pas enregistrée car vous n\'êtes pas connecté.');
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
