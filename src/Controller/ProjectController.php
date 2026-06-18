<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'project_index')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $em->getRepository(Project::class)->findBy([], ['updatedAt' => 'DESC']),
        ]);
    }

    #[Route('/projects/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $project = new Project();
        if ($request->isMethod('POST')) {
            $this->fill($project, $request, $em);
            $project->owner ??= $this->getUser();
            $project->members->add($this->getUser());
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->id]);
        }

        return $this->render('project/form.html.twig', ['project' => $project, 'users' => $em->getRepository(User::class)->findAll()]);
    }

    #[Route('/projects/{id}', name: 'project_show')]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', ['project' => $project]);
    }

    #[Route('/projects/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $this->fill($project, $request, $em);
            $project->updatedAt = new \DateTimeImmutable();
            $em->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->id]);
        }

        return $this->render('project/form.html.twig', ['project' => $project, 'users' => $em->getRepository(User::class)->findAll()]);
    }

    #[Route('/projects/{id}/delete', name: 'project_delete')]
    public function delete(Project $project, EntityManagerInterface $em): Response
    {
        $em->remove($project);
        $em->flush();

        return $this->redirectToRoute('project_index');
    }

    #[Route('/projects/{id}/members/add', name: 'project_member_add')]
    public function addMember(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($request->query->get('user'));
        if ($user) {
            $project->members->add($user);
            $em->flush();
        }

        return $this->redirectToRoute('project_show', ['id' => $project->id]);
    }

    #[Route('/projects/{id}/members/remove', name: 'project_member_remove')]
    public function removeMember(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($request->query->get('user'));
        if ($user) {
            $project->members->removeElement($user);
            $em->flush();
        }

        return $this->redirectToRoute('project_show', ['id' => $project->id]);
    }

    #[Route('/projects/{id}/status', name: 'project_status')]
    public function status(Project $project, Request $request, EntityManagerInterface $em): Response
    {
        $project->status = (string) $request->query->get('status', 'active');
        $project->updatedAt = new \DateTimeImmutable();
        $em->flush();

        return $this->redirectToRoute('project_show', ['id' => $project->id]);
    }

    #[Route('/projects.csv', name: 'project_export_csv')]
    public function export(EntityManagerInterface $em): Response
    {
        $rows = ['id,name,status,budget,owner,description'];
        foreach ($em->getRepository(Project::class)->findAll() as $project) {
            $rows[] = "{$project->id},{$project->name},{$project->status},{$project->budget},{$project->owner?->email},{$project->description}";
        }

        return new Response(implode("\n", $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="projects.csv"',
        ]);
    }

    private function fill(Project $project, Request $request, EntityManagerInterface $em): void
    {
        foreach (['name', 'description', 'status'] as $field) {
            $project->$field = (string) $request->request->get($field, $project->$field);
        }
        $project->budget = (int) $request->request->get('budget', $project->budget);
        $project->confidential = (bool) $request->request->get('confidential', $project->confidential);
        if ($request->request->get('owner_id')) {
            $project->owner = $em->getRepository(User::class)->find($request->request->get('owner_id'));
        }
    }
}
