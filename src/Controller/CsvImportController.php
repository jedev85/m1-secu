<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CsvImportController extends AbstractController
{
    #[Route('/imports/csv', name: 'csv_import', methods: ['GET', 'POST'])]
    public function import(Request $request, EntityManagerInterface $em): Response
    {
        $projectDir = (string) $this->getParameter('kernel.project_dir');
        $preview = [];
        $error = null;
        if ($request->isMethod('POST')) {
            try {
                $file = $request->files->get('file');
                $type = (string) $request->request->get('type', 'clients');
                $targetDir = $projectDir.'/public/uploads/imports';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $name = $file->getClientOriginalName();
                $file->move($targetDir, $name);
                $rows = array_map('str_getcsv', file($targetDir.'/'.$name));
                $preview = array_slice($rows, 0, 10);
                if ($request->request->get('commit')) {
                    foreach (array_slice($rows, 1) as $row) {
                        if ($type === 'projects') {
                            $project = new Project();
                            $project->name = $row[0] ?? 'Sans nom';
                            $project->description = $row[1] ?? '';
                            $project->budget = (int) ($row[2] ?? 0);
                            $project->owner = $this->getUser();
                            $em->persist($project);
                        } else {
                            $client = new Client();
                            $client->name = $row[0] ?? 'Sans nom';
                            $client->email = $row[1] ?? '';
                            $client->company = $row[2] ?? '';
                            $client->phone = $row[3] ?? '';
                            $client->notes = $row[4] ?? '';
                            $client->owner = $this->getUser();
                            $em->persist($client);
                        }
                    }
                    $em->flush();
                }
            } catch (\Throwable $e) {
                $error = $e->getMessage().' in '.$e->getFile().':'.$e->getLine();
            }
        }

        return $this->render('import/csv.html.twig', compact('preview', 'error'));
    }
}
