<?php

namespace App\Controller;

use App\Entity\WebhookEndpoint;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController extends AbstractController
{
    #[Route('/webhooks', name: 'webhook_index')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('webhook/index.html.twig', ['webhooks' => $em->getRepository(WebhookEndpoint::class)->findAll()]);
    }

    #[Route('/webhooks/new', name: 'webhook_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $webhook = new WebhookEndpoint();
        if ($request->isMethod('POST')) {
            $webhook->name = (string) $request->request->get('name');
            $webhook->url = (string) $request->request->get('url');
            $webhook->eventType = (string) $request->request->get('event_type');
            $webhook->secret = (string) $request->request->get('secret');
            $webhook->active = (bool) $request->request->get('active', true);
            $webhook->createdBy = $this->getUser();
            $em->persist($webhook);
            $em->flush();

            return $this->redirectToRoute('webhook_index');
        }

        return $this->render('webhook/form.html.twig', ['webhook' => $webhook]);
    }

    #[Route('/webhooks/{id}/test', name: 'webhook_test')]
    public function test(WebhookEndpoint $webhook): Response
    {
        $result = '';
        try {
            $response = HttpClient::create(['timeout' => 3, 'verify_peer' => false, 'verify_host' => false])->request('POST', $webhook->url, [
                'json' => ['event' => $webhook->eventType, 'secret' => $webhook->secret, 'sample' => true],
            ]);
            $result = $response->getStatusCode().' '.$response->getContent(false);
        } catch (\Throwable $e) {
            $result = $e->getMessage();
        }

        return $this->render('webhook/test.html.twig', ['webhook' => $webhook, 'result' => $result]);
    }

    #[Route('/webhooks/{id}/delete', name: 'webhook_delete')]
    public function delete(WebhookEndpoint $webhook, EntityManagerInterface $em): Response
    {
        $em->remove($webhook);
        $em->flush();

        return $this->redirectToRoute('webhook_index');
    }
}
