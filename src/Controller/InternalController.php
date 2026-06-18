<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class InternalController extends AbstractController
{
    #[Route('/internal/health', name: 'internal_health')]
    public function health(): JsonResponse
    {
        return $this->json(['status' => 'ok', 'node' => 'auditlab-local', 'time' => date(DATE_ATOM)]);
    }

    #[Route('/internal/debug', name: 'internal_debug')]
    public function debug(): JsonResponse
    {
        return $this->json(['env' => $_ENV, 'server' => $_SERVER]);
    }

    #[Route('/internal/metadata', name: 'internal_metadata')]
    public function metadata(): JsonResponse
    {
        return $this->json([
            'service' => 'auditlab-symfony',
            'backup_bucket' => 's3://pme-internal-backups',
            'db_hint' => 'sqlite var/auditlab.db',
            'internal_token' => 'training-token-do-not-use-in-production',
        ]);
    }
}
