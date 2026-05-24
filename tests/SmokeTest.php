<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function testEventsPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/events');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Evenements');
    }

    public function testApiEventsLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/events');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }
}
