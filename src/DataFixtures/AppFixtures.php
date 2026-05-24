<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Invoice;
use App\Entity\Registration;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher, private string $invoicesDir) {}

    public function load(ObjectManager $manager): void
    {
        if (!is_dir($this->invoicesDir)) {
            mkdir($this->invoicesDir, 0775, true);
        }

        $users = [];
        $userData = [
            ['user1@example.test', 'Alice Martin', 'Blue Team Conseil', ['ROLE_USER'], 'VIP demo account, support contract: gold', '+33 1 23 45 67 01'],
            ['user2@example.test', 'Karim Bernard', 'SecOps Factory', ['ROLE_USER'], 'Delayed payment on previous workshop', '+33 1 23 45 67 02'],
            ['admin@example.test', 'Nora Admin', 'EventSecure', ['ROLE_ADMIN'], 'Internal administrator account', '+33 1 23 45 67 03'],
            ['lea.audit@example.test', 'Lea Dubois', 'Audit & Co', ['ROLE_USER'], 'Requested invoice export by email', '+33 1 23 45 67 04'],
            ['marc.dev@example.test', 'Marc Lefevre', 'DevSec Studio', ['ROLE_USER'], 'Interested in API access beta', '+33 1 23 45 67 05'],
            ['sara.ops@example.test', 'Sara Nguyen', 'Ops Hexa', ['ROLE_USER'], 'Uses SSO in real tenant, local password for lab', '+33 1 23 45 67 06'],
            ['hugo.red@example.test', 'Hugo Petit', 'Red Team School', ['ROLE_USER'], 'Asked for advanced workshop material', '+33 1 23 45 67 07'],
            ['support@example.test', 'Support Demo', 'EventSecure', ['ROLE_USER'], 'Support account with excessive internal note', '+33 1 23 45 67 08'],
        ];
        foreach ($userData as [$email, $name, $company, $roles, $note, $phone]) {
            $user = (new User())
                ->setEmail($email)
                ->setFullName($name)
                ->setCompany($company)
                ->setPhone($phone)
                ->setRoles($roles)
                ->setInternalNote($note);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        $events = [];
        $eventData = [
            ['OWASP Top 10 en pratique', 'Paris', 'conference', 49000, '+7 days', 32, 'Cartographie des risques web, lecture de code et priorisation.'],
            ['Atelier SQL Injection defensive', 'Lyon', 'workshop', 39000, '+14 days', 20, 'Recherche vulnerable, requetes parametrees et tests de non-regression.'],
            ['XSS, CSRF et navigateur', 'Nantes', 'workshop', 35000, '+21 days', 22, 'Commentaires, contexte HTML, formulaires POST et controles navigateur.'],
            ['DevSecOps et pipeline CI', 'Remote', 'webinar', 19000, '+28 days', 80, 'Definition of done, audit Composer, tests et qualite de livraison.'],
            ['Audit API et SSRF', 'Toulouse', 'conference', 42000, '+35 days', 28, 'BOLA, exposition de donnees, CORS et preview URL cote serveur.'],
            ['Fuzzing introduction', 'Grenoble', 'lab', 29000, '+42 days', 18, 'Corpus, crash, AddressSanitizer et regression sur mini parseur C.'],
            ['Threat modeling produit evenementiel', 'Bordeaux', 'workshop', 31000, '+49 days', 24, 'Modeliser acteurs, actifs, flux et abus dans un outil metier.'],
            ['Hardening Symfony et Nginx', 'Lille', 'workshop', 37000, '+56 days', 24, 'Headers, cookies, logs, erreurs et configuration environnement.'],
            ['Gestion des secrets applicatifs', 'Remote', 'webinar', 15000, '+63 days', 100, 'Cycle de vie des secrets, variables locales et detection accidentelle.'],
            ['Audit final accompagne', 'Marseille', 'lab', 59000, '+70 days', 16, 'Restitution, preuves, priorisation et defense des remediations.'],
            ['Atelier incident logs', 'Rennes', 'workshop', 26000, '+77 days', 24, 'Reconstituer une action sensible avec des logs imparfaits.'],
            ['Secure upload clinic', 'Strasbourg', 'workshop', 28000, '+84 days', 24, 'Validation fichier, stockage, nommage et diffusion controlee.'],
        ];
        foreach ($eventData as [$title, $location, $category, $price, $date, $capacity, $description]) {
            $event = (new Event())
                ->setTitle($title)
                ->setLocation($location)
                ->setCategory($category)
                ->setPriceCents($price)
                ->setCapacity($capacity)
                ->setStartsAt(new \DateTimeImmutable($date))
                ->setDescription($description);
            $manager->persist($event);
            $events[] = $event;
        }

        foreach ([
            [0, 0], [0, 1], [0, 4],
            [1, 1], [1, 2], [1, 5],
            [2, 0], [2, 4], [2, 9],
            [3, 3], [3, 6],
            [4, 4], [4, 7],
            [5, 7], [5, 8],
            [6, 5], [6, 9],
            [7, 10], [7, 11],
        ] as [$userIndex, $eventIndex]) {
            $manager->persist((new Registration())->setUser($users[$userIndex])->setEvent($events[$eventIndex]));
        }

        foreach ([
            [0, 0, 'Tres bon format, hate de pratiquer.'],
            [1, 0, 'Peut-on ajouter une partie API ?'],
            [3, 1, 'La partie correction SQL sera utile pour notre projet interne.'],
            [4, 2, 'Je veux comprendre les impacts navigateur sans sortir du lab.'],
            [5, 4, 'Interessant pour comparer API publique et API interne.'],
            [6, 5, 'Le native-lab aide a connecter web et memoire.'],
            [7, 7, 'Merci de prevoir une checklist hardening exploitable.'],
        ] as [$userIndex, $eventIndex, $content]) {
            $manager->persist((new Comment())->setAuthor($users[$userIndex])->setEvent($events[$eventIndex])->setContent($content));
        }

        $invoiceRows = [
            [$users[0], 'INV-2026-0001', 49000, 'OWASP Top 10 en pratique'],
            [$users[0], 'INV-2026-0002', 39000, 'Atelier SQL Injection defensive'],
            [$users[1], 'INV-2026-0003', 35000, 'XSS, CSRF et navigateur'],
            [$users[2], 'INV-2026-ADM', 0, 'Compte administration'],
            [$users[3], 'INV-2026-0004', 19000, 'DevSecOps et pipeline CI'],
            [$users[4], 'INV-2026-0005', 42000, 'Audit API et SSRF'],
            [$users[5], 'INV-2026-0006', 37000, 'Hardening Symfony et Nginx'],
            [$users[6], 'INV-2026-0007', 29000, 'Fuzzing introduction'],
            [$users[7], 'INV-2026-0008', 26000, 'Atelier incident logs'],
        ];
        foreach ($invoiceRows as [$user, $number, $amount, $label]) {
            $file = $number.'.txt';
            file_put_contents($this->invoicesDir.'/'.$file, "Facture fictive {$number}\nClient: {$user->getEmail()}\nPrestation: {$label}\nMontant cents: {$amount}\n");
            $manager->persist((new Invoice())->setUser($user)->setNumber($number)->setAmountCents($amount)->setFilePath($file));
        }

        $manager->flush();
    }
}
