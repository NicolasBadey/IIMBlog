<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Functional;

use Symfony\Component\Panther\PantherTestCase;

class UserWorkflowTest extends PantherTestCase
{
    public function testHome()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $this->assertStringContainsString('Welcom', $crawler->filter('main')->text());
    }

    public function testRegister()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/register');

        $this->assertStringContainsString('Register', $crawler->filter('main')->text());

        $form = $crawler->filter('form[name=registration_form]')->form([
            'registration_form[username]' => 'test@test.com',
            'registration_form[plainPassword]' => 'Password42',
        ]);
        $crawler = $client->submit($form);

        $this->assertStringContainsString('Welcome test@test.com', $crawler->filter('main')->text());
    }

    public function testlogout()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/logout');

        $this->assertStringNotContainsString('Welcome test@test.com', $crawler->filter('main')->text());
        $this->assertStringContainsString('Welcome', $crawler->filter('main')->text());
    }

    public function testLogin()
    {
        $client = static::createPantherClient();

        $crawler = $client->request('GET', '/login');

        $this->assertStringContainsString('Please sign in', $crawler->filter('main')->text());

        $form = $crawler->filter('#login')->form([
            'username' => 'test@test.com',
            'password' => 'Password42',
        ]);
        $crawler = $client->submit($form);

        $this->assertStringContainsString('Welcome test@test.com', $crawler->filter('main')->text());
    }
}
