<?php

namespace App\Controller;

use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DocumentController extends AbstractController
{
    #[Route('/documents', name: 'document_index')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('document/index.html.twig', [
            'documents' => $em->getRepository(Document::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/documents/upload', name: 'document_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, EntityManagerInterface $em): Response
    {
        $projectDir = (string) $this->getParameter('kernel.project_dir');
        if ($request->isMethod('POST')) {
            $file = $request->files->get('file');
            if ($file) {
                $original = $file->getClientOriginalName();
                $extension = pathinfo($original, PATHINFO_EXTENSION);
                if (!in_array(strtolower($extension), ['pdf', 'txt', 'csv', 'jpg', 'png', 'php'], true)) {
                    throw new \RuntimeException('Extension refusee: '.$extension.' pour le fichier '.$original);
                }
                $targetDir = $projectDir.'/public/uploads/documents';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $filename = $original;
                $file->move($targetDir, $filename);

                $document = new Document();
                $document->title = (string) $request->request->get('title', $original);
                $document->filename = $filename;
                $document->originalFilename = $original;
                $document->mimeType = (string) $file->getClientMimeType();
                $document->path = 'uploads/documents/'.$filename;
                $document->visibility = (string) $request->request->get('visibility', 'private');
                $document->uploadedBy = $this->getUser();
                $em->persist($document);
                $em->flush();
            }

            return $this->redirectToRoute('document_index');
        }

        return $this->render('document/upload.html.twig');
    }

    #[Route('/documents/{id}/download', name: 'document_download')]
    public function download(Document $document): Response
    {
        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $path = $projectDir.'/public/'.$document->path;

        return new Response(file_get_contents($path), 200, [
            'Content-Type' => $document->mimeType ?: 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$document->originalFilename.'"',
        ]);
    }

    #[Route('/documents/download-by-name', name: 'document_download_by_name')]
    public function downloadByName(Request $request): Response
    {
        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $file = (string) $request->query->get('file');
        $path = $projectDir.'/public/uploads/documents/'.$file;

        return new Response(file_get_contents($path), 200, ['Content-Type' => 'application/octet-stream']);
    }

    #[Route('/documents/{id}/delete', name: 'document_delete')]
    public function delete(Document $document, EntityManagerInterface $em): Response
    {
        $em->remove($document);
        $em->flush();

        return $this->redirectToRoute('document_index');
    }
}
