<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class UrlResponseTest extends PantherTestCase
{
    /**
     * @dataProvider provideUrls
     * @param $url
     */
    public function testSomething($url)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideUrls(){
        return [
            ['/login'],
            ['/register'],
            ['/default'],
        ];
    }
}
