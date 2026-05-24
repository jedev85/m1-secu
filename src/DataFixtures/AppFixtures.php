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
        foreach ([
            ['user1@example.test', 'Alice Martin', 'Blue Team Conseil', ['ROLE_USER'], 'VIP demo account, support contract: gold'],
            ['user2@example.test', 'Karim Bernard', 'SecOps Factory', ['ROLE_USER'], 'Delayed payment on previous workshop'],
            ['admin@example.test', 'Nora Admin', 'EventSecure', ['ROLE_ADMIN'], 'Internal administrator account'],
        ] as [$email, $name, $company, $roles, $note]) {
            $user = (new User())
                ->setEmail($email)
                ->setFullName($name)
                ->setCompany($company)
                ->setPhone('+33 1 23 45 67 89')
                ->setRoles($roles)
                ->setInternalNote($note);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        $events = [];
        $data = [
            ['OWASP Top 10 en pratique', 'Paris', 'conference', 49000, '+7 days'],
            ['Atelier SQL Injection defensive', 'Lyon', 'workshop', 39000, '+14 days'],
            ['XSS, CSRF et navigateur', 'Nantes', 'workshop', 35000, '+21 days'],
            ['DevSecOps et pipeline CI', 'Remote', 'webinar', 19000, '+28 days'],
            ['Audit API et SSRF', 'Toulouse', 'conference', 42000, '+35 days'],
            ['Fuzzing introduction', 'Grenoble', 'lab', 29000, '+42 days'],
        ];
        foreach ($data as [$title, $location, $category, $price, $date]) {
            $event = (new Event())
                ->setTitle($title)
                ->setLocation($location)
                ->setCategory($category)
                ->setPriceCents($price)
                ->setCapacity(24)
                ->setStartsAt(new \DateTimeImmutable($date))
                ->setDescription('Session professionnelle avec cas fil rouge, exercices encadres et livrables courts.');
            $manager->persist($event);
            $events[] = $event;
        }

        $manager->persist((new Registration())->setUser($users[0])->setEvent($events[0]));
        $manager->persist((new Registration())->setUser($users[1])->setEvent($events[1]));
        $manager->persist((new Comment())->setAuthor($users[0])->setEvent($events[0])->setContent('Tres bon format, hate de pratiquer.'));
        $manager->persist((new Comment())->setAuthor($users[1])->setEvent($events[0])->setContent('Peut-on ajouter une partie API ?'));

        foreach ([[$users[0], 'INV-2026-0001', 49000], [$users[1], 'INV-2026-0002', 39000], [$users[2], 'INV-2026-ADM', 0]] as [$user, $number, $amount]) {
            $file = $number.'.txt';
            file_put_contents($this->invoicesDir.'/'.$file, "Facture fictive {$number}\nClient: {$user->getEmail()}\nMontant cents: {$amount}\n");
            $manager->persist((new Invoice())->setUser($user)->setNumber($number)->setAmountCents($amount)->setFilePath($file));
        }

        $manager->flush();
    }
}
