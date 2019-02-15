<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-01-29
 * Time: 18:22
 */

namespace App\Tests\Controller\Api;

use App\Entity\CheckList;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckListControllerTest extends WebTestCase
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

    public function testCreateAction()
    {
        $client = static::createClient();
        $data = [
            'name' => 'My List',
            'expire' => '2019-11-19'
        ];
        $client->request(
            'POST',
            '/api/checklist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
              'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertContains($data['name'], $client->getResponse()->getContent());
    }

    public function testCreateUnAuthorizedAction()
    {
        $client = static::createClient();
        $data = [
            'name' => 'My List',
            'expire' => '2019-11-19'
        ];
        $client->request(
            'POST',
            '/api/checklist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ],
            json_encode($data)
        );
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateFailAction()
    {
        $client = static::createClient();
        $data = [
            'name' => 'My List',
            'expire' => '2000000'
        ];
        $client->request(
            'POST',
            '/api/checklist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }



    public function testEditAction()
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'My List'])->getId();
        $client = static::createClient();
        $data = [
            'name' => 'My List New',
            'expire' => '2020-01-19'
        ];
        $client->request(
            'PUT',
            '/api/checklist/'.$listId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertContains($data['name'], $client->getResponse()->getContent());
    }

    public function testEditUnAuthorizedAction()
    {
        $client = static::createClient();
        $data = [
            'name' => 'My List1111',
            'expire' => '2019-11-19'
        ];
        $client->request(
            'POST',
            '/api/checklist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ],
            json_encode($data)
        );
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEditFailAction()
    {
        $client = static::createClient();
        $data = [
            'name' => 'My List11111',
            'expire' => '2000000'
        ];
        $client->request(
            'POST',
            '/api/checklist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testDeleteUnAuthorizedAction()
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'My List New'])->getId();
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/checklist/'.$listId,
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
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'My List New'])->getId();
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/checklist/'.$listId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDeleteNotFoundAction()
    {
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/checklist/100000',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
