<?php

namespace App\Tests\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class DeleteControllerTest extends ApiTestCase
{
    public function testBaseApiUrl(): void
    {
        $response = static::createClient()->request('GET', '/api');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/api']);
    }

    public function testDeleteEndpoint(): void
    {
        $response = static::createClient()->request('DELETE', '/api/delete');
        $this->assertResponseIsSuccessful();
    }
}
