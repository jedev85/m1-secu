<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function testLoginPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Connexion');
    }

    public function testRoutesAreRegistered(): void
    {
        self::bootKernel();
        $routes = self::getContainer()->get('router')->getRouteCollection();
        self::assertNotNull($routes->get('event_index'));
        self::assertNotNull($routes->get('api_preview_url'));
    }

    public function testTechnicalIdentifiersWereNotAccented(): void
    {
        $projectDir = dirname(__DIR__);
        $templates = implode("\n", [
            file_get_contents($projectDir.'/templates/event/show.html.twig'),
            file_get_contents($projectDir.'/templates/profile/show.html.twig'),
            file_get_contents($projectDir.'/templates/admin/reports.html.twig'),
            file_get_contents($projectDir.'/templates/base.html.twig'),
        ]);

        self::assertStringContainsString("path('comment_delete'", $templates);
        self::assertStringContainsString('app.user.roles', $templates);
        self::assertStringContainsString('user.roles', $templates);
        self::assertStringContainsString('<meta charset="utf-8">', $templates);
        self::assertStringNotContainsString('comment_delété', $templates);
        self::assertStringNotContainsString('rôles', $templates);
        self::assertStringNotContainsString('<metà', $templates);
    }
}
