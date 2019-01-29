<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-01-29
 * Time: 21:42
 */

namespace App\Tests\Controller\Api;

use App\Entity\CheckList;
use App\Entity\Label;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LabelControllerTest extends WebTestCase
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
            'name' => 'My Label',
        ];
        $client->request('POST', '/api/label',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertContains($data['name'], $client->getResponse()->getContent());

    }

    public function testEditAction()
    {
        $labelId = $this->entityManager->getRepository(Label::class)->findOneBy(['name' => 'My Label'])->getId();
        $client = static::createClient();
        $data = [
            'name' => 'My Label New',
        ];
        $client->request('PUT', '/api/label/'.$labelId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertContains($data['name'], $client->getResponse()->getContent());

    }

    public function testAddCheckListAction()
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name'])->getId();
        $labelId = $this->entityManager->getRepository(Label::class)->findOneBy(['name' => 'My Label New'])->getId();
        $client = static::createClient();
        $client->request('POST', '/api/label/'.$labelId.'/checklist/'.$listId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testRemoveCheckListAction()
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name'])->getId();
        $labelId = $this->entityManager->getRepository(Label::class)->findOneBy(['name' => 'My Label New'])->getId();
        $client = static::createClient();
        $client->request('DELETE', '/api/label/'.$labelId.'/checklist/'.$listId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testDeleteAction()
    {
        $labelId = $this->entityManager->getRepository(Label::class)->findOneBy(['name' => 'My Label New'])->getId();
        $client = static::createClient();
        $data = [
            'name' => 'My Label New',
        ];
        $client->request('DELETE', '/api/label/'.$labelId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ],
            json_encode($data)
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
}
