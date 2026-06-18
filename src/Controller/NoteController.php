<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\InternalNote;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{
    #[Route('/notes', name: 'note_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $visibility = $request->query->get('visibility');
        $criteria = $visibility ? ['visibility' => $visibility] : [];

        return $this->render('note/index.html.twig', [
            'notes' => $em->getRepository(InternalNote::class)->findBy($criteria, ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/notes/new', name: 'note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $note = new InternalNote();
        if ($request->isMethod('POST')) {
            $this->fill($note, $request, $em);
            $note->author = $this->getUser();
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('note_show', ['id' => $note->id]);
        }

        return $this->render('note/form.html.twig', $this->formData($em, $note));
    }

    #[Route('/notes/{id}', name: 'note_show')]
    public function show(InternalNote $note): Response
    {
        return $this->render('note/show.html.twig', ['note' => $note]);
    }

    #[Route('/notes/{id}/edit', name: 'note_edit', methods: ['GET', 'POST'])]
    public function edit(InternalNote $note, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $this->fill($note, $request, $em);
            $em->flush();

            return $this->redirectToRoute('note_show', ['id' => $note->id]);
        }

        return $this->render('note/form.html.twig', $this->formData($em, $note));
    }

    #[Route('/notes/{id}/delete', name: 'note_delete')]
    public function delete(InternalNote $note, EntityManagerInterface $em): Response
    {
        $em->remove($note);
        $em->flush();

        return $this->redirectToRoute('note_index');
    }

    private function fill(InternalNote $note, Request $request, EntityManagerInterface $em): void
    {
        $note->title = (string) $request->request->get('title');
        $note->content = (string) $request->request->get('content');
        $note->visibility = (string) $request->request->get('visibility', 'private');
        $note->relatedClient = $request->request->get('client_id') ? $em->getRepository(Client::class)->find($request->request->get('client_id')) : null;
        $note->relatedProject = $request->request->get('project_id') ? $em->getRepository(Project::class)->find($request->request->get('project_id')) : null;
    }

    private function formData(EntityManagerInterface $em, InternalNote $note): array
    {
        return ['note' => $note, 'clients' => $em->getRepository(Client::class)->findAll(), 'projects' => $em->getRepository(Project::class)->findAll()];
    }
}
