<?php

namespace App\DataFixtures;

use App\Entity\AppSetting;
use App\Entity\AuditLog;
use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Document;
use App\Entity\Invoice;
use App\Entity\Ticket;
use App\Entity\User;
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
        foreach ([
            'user1@example.com' => ['Jean Martin', ['ROLE_USER'], 'Commercial'],
            'user2@example.com' => ['Amina Benali', ['ROLE_USER'], 'Operations'],
            'support@example.com' => ['Sophie Support', ['ROLE_SUPPORT'], 'Support'],
            'manager@example.com' => ['Marc Manager', ['ROLE_MANAGER'], 'Direction'],
            'admin@example.com' => ['Alice Admin', ['ROLE_ADMIN'], 'IT'],
        ] as $email => [$name, $roles, $department]) {
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

        $clients = [];
        foreach ([
            ['Atelier Nova', 'contact@atelier-nova.test', '01 42 00 10 10', 'Atelier Nova', 'Contrat support premium. Contact DAF: claire@atelier-nova.test', $users['user1@example.com']],
            ['Bureau Atlas', 'it@bureau-atlas.test', '01 43 00 20 20', 'Bureau Atlas', 'Relance comptable a prevoir avant fin de mois.', $users['user1@example.com']],
            ['Cabinet Rivage', 'secretariat@rivage.test', '02 40 33 10 22', 'Cabinet Rivage', 'Client sensible, facturation trimestrielle.', $users['user2@example.com']],
            ['Delta Retail', 'ops@delta-retail.test', '04 72 11 22 33', 'Delta Retail', '<strong>Migration ERP en cours</strong>', $users['user2@example.com']],
            ['Helios Sante', 'admin@helios-sante.test', '05 61 44 55 66', 'Helios Sante', 'Donnees de sante simulees: ne pas exposer.', $users['manager@example.com']],
        ] as $row) {
            [$name, $email, $phone, $company, $notes, $owner] = $row;
            $client = new Client();
            $client->name = $name;
            $client->email = $email;
            $client->phone = $phone;
            $client->company = $company;
            $client->notes = $notes;
            $client->owner = $owner;
            $manager->persist($client);
            $clients[] = $client;
        }

        $tickets = [];
        foreach ([
            ['VPN inaccessible', 'Erreur MFA depuis le reseau invite.', 'open', 'high', $clients[0], $users['user1@example.com'], $users['support@example.com']],
            ['Export comptable', 'Demande export CSV factures mai.', 'pending', 'normal', $clients[1], $users['user1@example.com'], null],
            ['Acces application RH', 'Compte bloque apres changement email.', 'open', 'normal', $clients[2], $users['user2@example.com'], $users['support@example.com']],
            ['Incident confidentialite', 'Un document prive est visible depuis une URL directe.', 'open', 'high', $clients[4], $users['manager@example.com'], $users['admin@example.com']],
        ] as $row) {
            [$title, $description, $status, $priority, $client, $creator, $assignee] = $row;
            $ticket = new Ticket();
            $ticket->title = $title;
            $ticket->description = $description;
            $ticket->status = $status;
            $ticket->priority = $priority;
            $ticket->customer = $client;
            $ticket->createdBy = $creator;
            $ticket->assignedTo = $assignee;
            $manager->persist($ticket);
            $tickets[] = $ticket;
        }

        foreach ([
            ['Nous avons reproduit le probleme sur Firefox.', $users['support@example.com'], $tickets[0]],
            ['Merci de traiter avant vendredi.', $users['user1@example.com'], $tickets[1]],
            ['Le client signale que le message persiste apres deconnexion.', $users['user2@example.com'], $tickets[2]],
            ['Verifier la configuration locale avant cloture.', $users['admin@example.com'], $tickets[3]],
        ] as [$content, $author, $ticket]) {
            $comment = new Comment();
            $comment->content = $content;
            $comment->author = $author;
            $comment->ticket = $ticket;
            $manager->persist($comment);
        }

        foreach ([
            ['INV-2026-001', $clients[0], 12500, 'sent', $users['user1@example.com']],
            ['INV-2026-002', $clients[1], 7800, 'paid', $users['user1@example.com']],
            ['INV-2026-003', $clients[2], 21900, 'draft', $users['user2@example.com']],
            ['INV-2026-004', $clients[4], 45200, 'late', $users['manager@example.com']],
        ] as [$number, $client, $amount, $status, $owner]) {
            $invoice = new Invoice();
            $invoice->number = $number;
            $invoice->client = $client;
            $invoice->amount = $amount;
            $invoice->status = $status;
            $invoice->pdfPath = 'private/invoices/'.$number.'.pdf';
            $invoice->owner = $owner;
            $manager->persist($invoice);
        }

        foreach ([
            ['Procedure VPN', 'procedure-vpn.txt', 'text/plain', 'uploads/documents/procedure-vpn.txt', 'team', $users['support@example.com']],
            ['Contrat Helios', 'contrat-helios-private.pdf', 'application/pdf', 'uploads/documents/contrat-helios-private.pdf', 'private', $users['manager@example.com']],
            ['Export technique', 'diagnostic.csv', 'text/csv', 'uploads/documents/diagnostic.csv', 'public', $users['admin@example.com']],
        ] as [$title, $filename, $mime, $path, $visibility, $owner]) {
            $document = new Document();
            $document->title = $title;
            $document->filename = $filename;
            $document->originalFilename = $filename;
            $document->mimeType = $mime;
            $document->path = $path;
            $document->visibility = $visibility;
            $document->uploadedBy = $owner;
            $manager->persist($document);
        }

        foreach ([
            ['info', 'auth', 'Login success for admin@example.com from 127.0.0.1'],
            ['warning', 'import', 'URL import failed during client synchronization timeout=3'],
            ['error', 'payment', 'Stripe test key sk_test_51_FAKE_training_secret leaked in stack trace'],
            ['debug', 'db', 'DATABASE_URL=sqlite:///var/auditlab.db APP_SECRET=local-auditlab-training-secret'],
        ] as [$level, $source, $message]) {
            $log = new AuditLog();
            $log->level = $level;
            $log->source = $source;
            $log->message = $message;
            $manager->persist($log);
        }

        foreach ([
            ['maintenance_mode', '0', false],
            ['support_email', 'support-internal@example.com', false],
            ['legacy_api_token', 'legacy-token-123456-local-training', true],
            ['invoice_export_secret', 'csv-export-secret-local', true],
        ] as [$name, $value, $sensitive]) {
            $setting = new AppSetting();
            $setting->name = $name;
            $setting->value = $value;
            $setting->sensitive = $sensitive;
            $manager->persist($setting);
        }

        $manager->flush();
    }
}
