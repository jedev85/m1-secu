<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class TeacherSecurityExamplesTest extends TestCase
{
    public function testPrivateNetworkExamplesAreDocumentedForSsrfMitigation(): void
    {
        $guide = file_get_contents(dirname(__DIR__).'/docs/CORRECTIONS.md');
        self::assertStringContainsString('127.0.0.0/8', $guide);
        self::assertStringContainsString('192.168.0.0/16', $guide);
    }
}
