<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $criteria = [];
        if ($request->query->get('assignedTo')) {
            $criteria['assignedTo'] = $em->getRepository(User::class)->find($request->query->get('assignedTo'));
        }

        return $this->render('task/index.html.twig', ['tasks' => $em->getRepository(Task::class)->findBy($criteria)]);
    }

    #[Route('/tasks/mine', name: 'task_mine')]
    public function mine(EntityManagerInterface $em): Response
    {
        return $this->render('task/index.html.twig', ['tasks' => $em->getRepository(Task::class)->findBy(['assignedTo' => $this->getUser()])]);
    }

    #[Route('/tasks/new', name: 'task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        if ($request->isMethod('POST')) {
            $this->fill($task, $request, $em);
            $task->createdBy = $this->getUser();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_show', ['id' => $task->id]);
        }

        return $this->render('task/form.html.twig', $this->formData($em, $task));
    }

    #[Route('/tasks/{id}', name: 'task_show')]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', ['task' => $task]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $this->fill($task, $request, $em);
            $em->flush();

            return $this->redirectToRoute('task_show', ['id' => $task->id]);
        }

        return $this->render('task/form.html.twig', $this->formData($em, $task));
    }

    #[Route('/tasks/{id}/status', name: 'task_status')]
    public function status(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $task->status = (string) $request->query->get('status', 'done');
        $em->flush();

        return $this->redirectToRoute('task_show', ['id' => $task->id]);
    }

    #[Route('/tasks/{id}/assign', name: 'task_assign')]
    public function assign(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $task->assignedTo = $em->getRepository(User::class)->find($request->query->get('user'));
        $em->flush();

        return $this->redirectToRoute('task_show', ['id' => $task->id]);
    }

    #[Route('/tasks/{id}/comment', name: 'task_comment', methods: ['POST'])]
    public function comment(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $task->description .= "\n\nCommentaire: ".$request->request->get('comment');
        $em->flush();

        return $this->redirectToRoute('task_show', ['id' => $task->id]);
    }

    private function fill(Task $task, Request $request, EntityManagerInterface $em): void
    {
        foreach (['title', 'description', 'status', 'priority'] as $field) {
            $task->$field = (string) $request->request->get($field, $task->$field);
        }
        $task->project = $em->getRepository(Project::class)->find($request->request->get('project_id'));
        $task->assignedTo = $request->request->get('assigned_to') ? $em->getRepository(User::class)->find($request->request->get('assigned_to')) : null;
        $task->dueDate = $request->request->get('due_date') ? new \DateTimeImmutable($request->request->get('due_date')) : null;
    }

    private function formData(EntityManagerInterface $em, Task $task): array
    {
        return ['task' => $task, 'projects' => $em->getRepository(Project::class)->findAll(), 'users' => $em->getRepository(User::class)->findAll()];
    }
}
