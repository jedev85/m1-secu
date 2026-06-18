<?php

namespace App\Controller;

use App\Entity\AppSetting;
use App\Entity\ActivityLog;
use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Message;
use App\Entity\Project;
use App\Entity\Setting;
use App\Entity\Task;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api/clients', name: 'api_clients')]
    public function clients(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (Client $client) => [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'company' => $client->company,
            'notes' => $client->notes,
            'owner' => ['id' => $client->owner?->id, 'email' => $client->owner?->email, 'roles' => $client->owner?->roles],
        ], $em->getRepository(Client::class)->findAll()));
    }

    #[Route('/api/clients/{id}', name: 'api_client_show')]
    public function client(Client $client): JsonResponse
    {
        return $this->json([
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'company' => $client->company,
            'notes' => $client->notes,
            'createdAt' => $client->createdAt->format(DATE_ATOM),
            'owner' => ['id' => $client->owner?->id, 'email' => $client->owner?->email, 'roles' => $client->owner?->roles],
        ]);
    }

    #[Route('/api/tickets', name: 'api_tickets')]
    public function tickets(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (Ticket $ticket) => [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'customerEmail' => $ticket->customer?->email,
            'createdBy' => $ticket->createdBy?->email,
            'comments' => array_map(fn ($comment) => $comment->content, $ticket->comments->toArray()),
        ], $em->getRepository(Ticket::class)->findAll()));
    }

    #[Route('/api/invoices/{id}', name: 'api_invoice_show')]
    public function invoice(Invoice $invoice): JsonResponse
    {
        return $this->json([
            'id' => $invoice->id,
            'number' => $invoice->number,
            'amount' => $invoice->amount,
            'status' => $invoice->status,
            'pdfPath' => $invoice->pdfPath,
            'owner' => $invoice->owner?->email,
            'client' => ['name' => $invoice->client?->name, 'email' => $invoice->client?->email, 'notes' => $invoice->client?->notes],
        ]);
    }

    #[Route('/api/debug/config', name: 'api_debug_config')]
    public function debugConfig(EntityManagerInterface $em): JsonResponse
    {
        return $this->json([
            'settings' => array_map(fn (AppSetting $setting) => [$setting->name => $setting->value], $em->getRepository(AppSetting::class)->findAll()),
            'database_url' => $_ENV['DATABASE_URL'] ?? null,
            'app_secret' => $_ENV['APP_SECRET'] ?? null,
        ]);
    }

    #[Route('/api/profile', name: 'api_profile')]
    public function profile(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->json(['id' => $user->id, 'email' => $user->email, 'roles' => $user->roles, 'department' => $user->department, 'bio' => $user->bio]);
    }

    #[Route('/api/users', name: 'api_users')]
    public function users(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (User $user) => [
            'id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles,
            'fullName' => $user->fullName,
            'department' => $user->department,
        ], $em->getRepository(User::class)->findAll()));
    }

    #[Route('/api/users/{id}', name: 'api_user_show')]
    public function user(User $user): JsonResponse
    {
        return $this->json(['id' => $user->id, 'email' => $user->email, 'roles' => $user->roles, 'fullName' => $user->fullName, 'bio' => $user->bio]);
    }

    #[Route('/api/projects', name: 'api_projects')]
    public function projects(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $payload = json_decode($request->getContent(), true) ?: [];
            $project = new Project();
            foreach ($payload as $field => $value) {
                if (property_exists($project, $field) && !in_array($field, ['id', 'members', 'owner'], true)) {
                    $project->$field = $field === 'budget' ? (int) $value : $value;
                }
            }
            $project->owner = $this->getUser();
            $em->persist($project);
            $em->flush();
        }

        return $this->json(array_map(fn (Project $project) => [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'budget' => $project->budget,
            'status' => $project->status,
            'confidential' => $project->confidential,
            'owner' => $project->owner?->email,
            'members' => array_map(fn (User $user) => $user->email, $project->members->toArray()),
        ], $em->getRepository(Project::class)->findAll()));
    }

    #[Route('/api/projects/{id}', name: 'api_project_show')]
    public function project(Project $project): JsonResponse
    {
        return $this->json([
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'budget' => $project->budget,
            'status' => $project->status,
            'confidential' => $project->confidential,
            'owner' => ['id' => $project->owner?->id, 'email' => $project->owner?->email, 'roles' => $project->owner?->roles],
        ]);
    }

    #[Route('/api/tasks', name: 'api_tasks')]
    public function tasks(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (Task $task) => [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'project' => $task->project?->name,
            'assignedTo' => $task->assignedTo?->email,
            'createdBy' => $task->createdBy?->email,
        ], $em->getRepository(Task::class)->findAll()));
    }

    #[Route('/api/messages/{id}', name: 'api_message_show')]
    public function message(Message $message): JsonResponse
    {
        return $this->json([
            'id' => $message->id,
            'sender' => $message->sender?->email,
            'recipient' => $message->recipient?->email,
            'subject' => $message->subject,
            'body' => $message->body,
            'readAt' => $message->readAt?->format(DATE_ATOM),
        ]);
    }

    #[Route('/api/settings', name: 'api_settings')]
    public function settings(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (Setting $setting) => [
            'id' => $setting->id,
            'keyName' => $setting->keyName,
            'value' => $setting->value,
            'isSensitive' => $setting->isSensitive,
            'updatedBy' => $setting->updatedBy?->email,
        ], $em->getRepository(Setting::class)->findAll()));
    }

    #[Route('/api/admin/logs', name: 'api_admin_logs')]
    public function adminLogs(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (ActivityLog $log) => [
            'id' => $log->id,
            'actor' => $log->actor?->email,
            'action' => $log->action,
            'targetType' => $log->targetType,
            'targetId' => $log->targetId,
            'ipAddress' => $log->ipAddress,
            'userAgent' => $log->userAgent,
            'details' => $log->details,
        ], $em->getRepository(ActivityLog::class)->findAll()));
    }
}
