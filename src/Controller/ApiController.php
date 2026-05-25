<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Invoice;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/events', methods: ['GET'])]
    public function events(EventRepository $events): JsonResponse
    {
        return $this->json(array_map(fn (Event $event) => [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'location' => $event->getLocation(),
            'startsAt' => $event->getStartsAt()->format(DATE_ATOM),
            'priceCents' => $event->getPriceCents(),
        ], $events->findPublished()));
    }

    #[Route('/users/{id}', methods: ['GET'])]
    public function user(User $user): JsonResponse
    {
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'fullName' => $user->getFullName(),
            'company' => $user->getCompany(),
            'phone' => $user->getPhone(),
            'roles' => $user->getRoles(),
            'internalNote' => $user->getInternalNote(),
        ]);
    }

    #[Route('/invoices/{id}', methods: ['GET'])]
    public function invoice(Invoice $invoice): JsonResponse
    {
        return $this->json([
            'id' => $invoice->getId(),
            'number' => $invoice->getNumber(),
            'amountCents' => $invoice->getAmountCents(),
            'userEmail' => $invoice->getUser()->getEmail(),
            'download' => '/invoices/'.$invoice->getId().'/download',
        ], 200, ['Access-Control-Allow-Origin' => '*']);
    }

    #[Route('/profile', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $payload = json_decode($request->getContent(), true) ?: [];
        foreach ($payload as $field => $value) {
            $setter = 'set'.ucfirst($field);
            if (method_exists($user, $setter)) {
                $user->{$setter}($value);
            }
        }
        $em->flush();
        return $this->json(['status' => 'updated', 'roles' => $user->getRoles()]);
    }

    #[Route('/preview-url', name: 'api_preview_url', methods: ['POST'])]
    public function preview(Request $request, HttpClientInterface $client): JsonResponse
    {
        $url = (string) ($request->request->get('url') ?: json_decode($request->getContent(), true)['url'] ?? '');
        $response = $client->request('GET', $url, ['timeout' => 3]);
        $body = substr($response->getContent(false), 0, 500);
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $body, $matches);
        return $this->json([
            'url' => $url,
            'status' => $response->getStatusCode(),
            'title' => trim(strip_tags($matches[1] ?? '')),
            'sample' => $body,
        ]);
    }
}
