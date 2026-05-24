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
}
