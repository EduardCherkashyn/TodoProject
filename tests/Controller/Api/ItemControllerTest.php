<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-01-29
 * Time: 21:00
 */

namespace App\Tests\Controller\Api;

use App\Entity\CheckList;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ItemControllerTest extends WebTestCase
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
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name'])->getId();
        $client = static::createClient();
        $data = [
            'checked' => true
        ];
        $client->request(
            'POST',
            '/api/list/'.$listId.'/item',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddUnAuthorizedAction()
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name'])->getId();
        $client = static::createClient();
        $data = [
            'checked' => true
        ];
        $client->request(
            'POST',
            '/api/list/'.$listId.'/item',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ],
            json_encode($data)
        );
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAddInvalidAction()
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name'])->getId();
        $client = static::createClient();
        $data = [
            'something' => '1111111111'
        ];
        $client->request(
            'POST',
            '/api/list/'.$listId.'/item',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testUpdateAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/list/'.$list->getId().'/item/'.$item->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdateUnAuthorizedAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/list/'.$list->getId().'/item/'.$item->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ]
        );
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testDeleteUnAuthorizedAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/list/'.$list->getId().'/item/'.$item->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ]
        );
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }


    public function testDeleteAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $item = $list->getItems()->last();
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/list/'.$list->getId().'/item/'.$item->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }


}
