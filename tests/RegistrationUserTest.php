<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $client->request('GET', '/inscription');

        $client->submitForm('Inscription', [
            'register[firstname]' => 'Jojo',
            'register[name]' => 'Joestar',
            'register[email]' => 'jj@jojo.fr',
            'register[plainPassword][first]' => '12345',
            'register[plainPassword][second]' => '12345',
        ]);

        $this->assertResponseRedirects('/connexion');
        $client->followRedirect();

        $this->assertSelectorExists('div:contains("Bienvenue à bord Moussaillon! Connecte-toi pour accéder au Marché.")');
    }
}
