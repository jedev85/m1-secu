<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportController extends AbstractController
{
    #[Route('/import/url', name: 'import_url', methods: ['GET', 'POST'])]
    public function import(Request $request): Response
    {
        $content = null;
        $error = null;
        if ($request->isMethod('POST')) {
            $url = (string) $request->request->get('url');
            try {
                $response = HttpClient::create([
                    'timeout' => 3,
                    'verify_peer' => false,
                    'verify_host' => false,
                ])->request('GET', $url);
                $content = $response->getContent(false);
            } catch (\Throwable $e) {
                $error = $e->getMessage().' in '.$e->getFile().':'.$e->getLine();
            }
        }

        return $this->render('import/url.html.twig', compact('content', 'error'));
    }
}
