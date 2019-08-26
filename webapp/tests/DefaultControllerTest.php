<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class DefaultControllerTest extends PantherTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

/*        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
        $this->assertEquals(1, $crawler->filter('html:contains("in")')->count());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $link = $crawler->filter('a:contains("Register")')->link();
        $crawler = $client->click($link);
        $this->assertContains('Hello DefaultController!',$client->getResponse()->getContent());*/

        $form = $crawler->selectButton('Sign in')->form();
        $form['email'] = 'prova@prova.com2';
        $form['password'] = '12345';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('a:contains("Logout")')->count());


    }
}
