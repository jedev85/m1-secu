<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class DocumentationController extends AbstractController
{
    #[Route('/docs/{path}', name: 'docs_show', requirements: ['path' => '.+'], methods: ['GET'])]
    public function show(string $path, KernelInterface $kernel): Response
    {
        $docsDir = realpath($kernel->getProjectDir().'/docs');
        $file = realpath($docsDir.'/'.$path);

        if ($docsDir === false || $file === false || !str_starts_with($file, $docsDir) || !is_file($file)) {
            throw $this->createNotFoundException();
        }

        return new Response(file_get_contents($file), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
