<?php

namespace App\Tests\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class CountControllerTest extends ApiTestCase
{
    public function testBaseApiUrl(): void
    {
        $response = static::createClient()->request('GET', '/api');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => '/api']);
    }

    public function testCountEndpoint(): void
    {
        $response = static::createClient()->request('GET', '/api/count');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['count' => 0]);
    }

    public function testQueryEndpoint(): void
    {
        $response = static::createClient()->request('GET', '/api/count?serviceNames%5B%5D=userservice&startDate=2020-03-15%2006%3A27%3A33&endDate=2023-03-15%2006%3A27%3A33&statusCode=200');
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['count' => 0]);
    }
}
