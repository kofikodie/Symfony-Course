<?php

namespace App\Tests;

use App\Entity\Video;
use Symfony\Component\Panther\PantherTestCase;

/**
 * @property  client
 */
class DatabaseConnTest extends PantherTestCase
{

    private $entityManager;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown()
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @dataProvider provideUrls
     * @param $url
     */
    public function testSomething($url)
    {
        $crawler = $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $video = $this->entityManager
            ->getRepository(Video::class)
            ->find(1);

        $this->entityManager->remove($video);
        $this->entityManager->flush();

        $this->assertNull($this->entityManager
            ->getRepository(Video::class)
            ->find(1));

    }

    public function provideUrls()
    {
        return [
            ['/login']
        ];
    }
}
