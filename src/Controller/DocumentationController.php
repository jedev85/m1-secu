<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class DocumentationController extends AbstractController
{
    #[Route('/docs', name: 'docs_index', methods: ['GET'])]
    public function index(KernelInterface $kernel): Response
    {
        $docsDir = realpath($kernel->getProjectDir().'/docs');
        if ($docsDir === false) {
            throw $this->createNotFoundException();
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($docsDir, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || $file->getExtension() !== 'md') {
                continue;
            }

            $relativePath = str_replace($docsDir.'/', '', $file->getPathname());
            $files[] = $relativePath;
        }
        sort($files);

        $items = array_map(
            fn (string $file): string => sprintf('<li><a href="/docs/%s">%s</a></li>', htmlspecialchars($file, ENT_QUOTES), htmlspecialchars($file)),
            $files
        );

        return new Response(
            '<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Documentation</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body><main class="container py-4"><h1>Documentation EventSecure Lab</h1><p>Documents pedagogiques locaux.</p><ul>'.implode('', $items).'</ul></main></body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

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
