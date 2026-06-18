<?php

namespace App\DataFixtures;

use App\Entity\ActivityLog;
use App\Entity\AppSetting;
use App\Entity\AuditLog;
use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Document;
use App\Entity\InternalNote;
use App\Entity\Invoice;
use App\Entity\Message;
use App\Entity\Project;
use App\Entity\Setting;
use App\Entity\Task;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\WebhookEndpoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        $userRows = [
            'user1@example.com' => ['Jean Martin', ['ROLE_USER'], 'Commercial'],
            'user2@example.com' => ['Amina Benali', ['ROLE_USER'], 'Operations'],
            'user3@example.com' => ['Noah Bernard', ['ROLE_USER'], 'Finance'],
            'user4@example.com' => ['Lea Durand', ['ROLE_USER'], 'Juridique'],
            'user5@example.com' => ['Hugo Petit', ['ROLE_USER'], 'Produit'],
            'support@example.com' => ['Sophie Support', ['ROLE_SUPPORT'], 'Support'],
            'support2@example.com' => ['Karim Support', ['ROLE_SUPPORT'], 'Support'],
            'manager@example.com' => ['Marc Manager', ['ROLE_MANAGER'], 'Direction'],
            'admin@example.com' => ['Alice Admin', ['ROLE_ADMIN'], 'IT'],
        ];

        foreach ($userRows as $email => [$name, $roles, $department]) {
            $user = new User();
            $user->email = $email;
            $user->fullName = $name;
            $user->roles = $roles;
            $user->department = $department;
            $user->bio = 'Compte de demonstration pour audit interne.';
            $user->password = $this->hasher->hashPassword($user, 'password');
            $manager->persist($user);
            $users[$email] = $user;
        }

        $owners = array_values($users);
        $clients = [];
        $clientNames = ['Atelier Nova', 'Bureau Atlas', 'Cabinet Rivage', 'Delta Retail', 'Helios Sante', 'Innotech Conseil', 'Jardin Urbain', 'Kappa Logistics', 'Lumen Energie', 'Mistral Media', 'Nadir Finance', 'Orion Habitat'];
        foreach ($clientNames as $i => $name) {
            $client = new Client();
            $client->name = $name;
            $client->email = 'contact'.($i + 1).'@'.strtolower(str_replace(' ', '-', $name)).'.test';
            $client->phone = '01 40 '.str_pad((string) ($i + 10), 2, '0', STR_PAD_LEFT).' '.str_pad((string) ($i + 20), 2, '0', STR_PAD_LEFT).' 00';
            $client->company = $name;
            $client->notes = $i % 4 === 0 ? 'Contrat sensible, validation manager requise.' : 'Suivi commercial standard.';
            $client->owner = $owners[$i % count($owners)];
            $manager->persist($client);
            $clients[] = $client;
        }

        $projects = [];
        foreach (['Migration ERP', 'Portail Client', 'Refonte Support', 'Audit Fournisseurs', 'Data Room Finance', 'Extranet Partenaires', 'Inventaire Assets', 'Conformite RGPD'] as $i => $name) {
            $project = new Project();
            $project->name = $name;
            $project->description = $i % 3 === 0 ? 'Projet prioritaire avec donnees confidentielles.' : 'Projet interne suivi par plusieurs equipes.';
            $project->budget = 15000 + ($i * 7200);
            $project->status = ['draft', 'active', 'blocked', 'closed'][$i % 4];
            $project->owner = $owners[$i % count($owners)];
            $project->confidential = $i % 3 === 0;
            $project->members->add($project->owner);
            $project->members->add($owners[($i + 2) % count($owners)]);
            $manager->persist($project);
            $projects[] = $project;
        }

        $tasks = [];
        for ($i = 1; $i <= 20; $i++) {
            $task = new Task();
            $task->title = 'Tache projet '.$i;
            $task->description = 'Action operationnelle a traiter pour le lot '.$i.'.';
            $task->status = ['todo', 'doing', 'review', 'done'][$i % 4];
            $task->priority = ['low', 'normal', 'high'][$i % 3];
            $task->dueDate = new \DateTimeImmutable('+'.$i.' days');
            $task->assignedTo = $owners[$i % count($owners)];
            $task->createdBy = $users['manager@example.com'];
            $task->project = $projects[$i % count($projects)];
            $manager->persist($task);
            $tasks[] = $task;
        }

        $tickets = [];
        for ($i = 1; $i <= 15; $i++) {
            $ticket = new Ticket();
            $ticket->title = 'Ticket support '.$i;
            $ticket->description = 'Demande client '.$i.' avec informations de diagnostic.';
            $ticket->status = ['open', 'pending', 'closed'][$i % 3];
            $ticket->priority = ['low', 'normal', 'high'][$i % 3];
            $ticket->customer = $clients[$i % count($clients)];
            $ticket->createdBy = $owners[$i % count($owners)];
            $ticket->assignedTo = $i % 2 === 0 ? $users['support@example.com'] : $users['support2@example.com'];
            $manager->persist($ticket);
            $tickets[] = $ticket;
        }

        for ($i = 1; $i <= 30; $i++) {
            $comment = new Comment();
            $comment->content = 'Commentaire de suivi '.$i.' avec contexte fonctionnel.';
            $comment->author = $owners[$i % count($owners)];
            $comment->ticket = $tickets[$i % count($tickets)];
            $manager->persist($comment);
        }

        for ($i = 1; $i <= 10; $i++) {
            $message = new Message();
            $message->sender = $owners[$i % count($owners)];
            $message->recipient = $owners[($i + 3) % count($owners)];
            $message->subject = 'Message interne '.$i;
            $message->body = 'Echange interne concernant le client '.$clients[$i % count($clients)]->name.'.';
            $message->readAt = $i % 2 === 0 ? new \DateTimeImmutable('-'.$i.' hours') : null;
            $manager->persist($message);
        }

        for ($i = 1; $i <= 10; $i++) {
            $invoice = new Invoice();
            $invoice->number = 'INV-2026-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $invoice->client = $clients[$i % count($clients)];
            $invoice->amount = 2500 + ($i * 1750);
            $invoice->status = ['draft', 'sent', 'paid', 'late'][$i % 4];
            $invoice->pdfPath = 'private/invoices/'.$invoice->number.'.pdf';
            $invoice->owner = $owners[$i % count($owners)];
            $manager->persist($invoice);
        }

        for ($i = 1; $i <= 12; $i++) {
            $document = new Document();
            $document->title = 'Document interne '.$i;
            $document->filename = 'document-'.$i.'.txt';
            $document->originalFilename = $document->filename;
            $document->mimeType = 'text/plain';
            $document->path = 'uploads/documents/'.$document->filename;
            $document->visibility = ['private', 'team', 'public'][$i % 3];
            $document->uploadedBy = $owners[$i % count($owners)];
            $manager->persist($document);
        }

        for ($i = 1; $i <= 12; $i++) {
            $note = new InternalNote();
            $note->title = 'Note interne '.$i;
            $note->content = 'Note metier sur le dossier '.$clients[$i % count($clients)]->name.'.';
            $note->visibility = ['private', 'team', 'management', 'public'][$i % 4];
            $note->author = $owners[$i % count($owners)];
            $note->relatedClient = $clients[$i % count($clients)];
            $note->relatedProject = $projects[$i % count($projects)];
            $manager->persist($note);
        }

        for ($i = 1; $i <= 8; $i++) {
            $webhook = new WebhookEndpoint();
            $webhook->name = 'Webhook '.$i;
            $webhook->url = $i % 2 === 0 ? 'https://127.0.0.1:8000/internal/health' : 'https://example.invalid/hook/'.$i;
            $webhook->eventType = ['invoice.paid', 'ticket.created', 'project.updated'][$i % 3];
            $webhook->secret = 'whsec_local_'.$i.'_training';
            $webhook->active = $i % 3 !== 0;
            $webhook->createdBy = $owners[$i % count($owners)];
            $manager->persist($webhook);
        }

        for ($i = 1; $i <= 10; $i++) {
            $log = new ActivityLog();
            $log->actor = $owners[$i % count($owners)];
            $log->action = ['login', 'client.update', 'invoice.export', 'role.change', 'webhook.test'][$i % 5];
            $log->targetType = ['User', 'Client', 'Invoice', 'Webhook'][$i % 4];
            $log->targetId = $i;
            $log->ipAddress = '127.0.0.'.$i;
            $log->userAgent = 'AuditLabBrowser/'.$i;
            $log->details = $i % 4 === 0 ? 'Action realisee avec jeton applicatif local.' : 'Operation metier standard.';
            $manager->persist($log);
        }

        foreach ([
            ['maintenance_mode', '0', false],
            ['support_email', 'support-internal@example.com', false],
            ['legacy_api_token', 'legacy-token-123456-local-training', true],
            ['invoice_export_secret', 'csv-export-secret-local', true],
            ['webhook_default_secret', 'webhook-default-local-secret', true],
            ['session_timeout_minutes', '480', false],
            ['feature_import_url', '1', false],
            ['backup_access_key', 'backup-key-local-training', true],
        ] as [$name, $value, $sensitive]) {
            $setting = new AppSetting();
            $setting->name = $name;
            $setting->value = $value;
            $setting->sensitive = $sensitive;
            $manager->persist($setting);

            $newSetting = new Setting();
            $newSetting->keyName = $name;
            $newSetting->value = $value;
            $newSetting->isSensitive = $sensitive;
            $newSetting->updatedBy = $users['admin@example.com'];
            $manager->persist($newSetting);
        }

        foreach ([
            ['info', 'auth', 'Login success for admin@example.com from 127.0.0.1'],
            ['warning', 'import', 'URL import failed during client synchronization timeout=3'],
            ['error', 'payment', 'Payment provider test key visible in local stack trace'],
            ['debug', 'db', 'Local SQLite database initialized for training'],
            ['info', 'webhook', 'Webhook test executed for ticket.created'],
            ['warning', 'files', 'Document download path fallback used'],
            ['info', 'admin', 'Settings page opened by manager@example.com'],
            ['debug', 'api', 'JSON response generated with extended fields'],
            ['error', 'support', 'Ticket status changed outside workflow'],
            ['info', 'project', 'Confidential project exported'],
        ] as [$level, $source, $message]) {
            $audit = new AuditLog();
            $audit->level = $level;
            $audit->source = $source;
            $audit->message = $message;
            $manager->persist($audit);
        }

        $manager->flush();
    }
}
