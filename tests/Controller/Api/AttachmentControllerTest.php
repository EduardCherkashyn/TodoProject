<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-01-29
 * Time: 21:29
 */

namespace App\Tests\Controller\Api;

use App\Entity\CheckList;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AttachmentControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testAddAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $client = static::createClient();
        $data = [
            'text' => 'My Text',
        ];
        $client->request(
            'POST',
            '/api/item/'.$item->getId().'/attachment',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertContains($data['text'], $client->getResponse()->getContent());
    }

    public function testAddUnAuthorizedAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $client = static::createClient();
        $data = [
            'text' => 'My Text',
        ];
        $client->request(
            'POST',
            '/api/item/'.$item->getId().'/attachment',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ],
            json_encode($data)
        );
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAddInvaledAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $client = static::createClient();
        $data = [
            'text121212' => 'My Text',
        ];
        $client->request(
            'POST',
            '/api/item/'.$item->getId().'/attachment',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRemoveUnAuthorizedAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $attachment = $item->getAttachment();
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/item/'.$item->getId().'/attachment/'.$attachment->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ]
        );
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testRemoveAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $attachment = $item->getAttachment();
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/item/'.$item->getId().'/attachment/'.$attachment->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
