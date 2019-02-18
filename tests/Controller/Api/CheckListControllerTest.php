<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-01-29
 * Time: 18:22
 */

namespace App\Tests\Controller\Api;

use App\Entity\CheckList;
use App\Tests\AbstractTest;

class CheckListControllerTest extends AbstractTest
{

    /**
     * @dataProvider checklistData
     */
    public function testCreateAction($a, $b, $expected)
    {
        $data = $a;
        $this->client->request(
            'POST',
            '/api/checklist',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
              'HTTP_X-API_KEY' => $b
            ],
            json_encode($data)
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function checklistData()
    {
        return [
            [['name'=>'My List','expire'=>'2019-11-19'],'my-api-token',200],
            [['name'=>'My List','expire'=>'2019-11-19'],'',403],
            [['name'=>'jhakjfhkhdskfhkladh','expire'=>'20191111111'],'my-api-token', 400],
        ];
    }

    /**
     * @dataProvider editData
     */
    public function testEditAction($a, $b, $expected)
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name'])->getId();
        $data = $a;
        $this->client->request(
            'PUT',
            '/api/checklist/'.$listId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => $b
            ],
            json_encode($data)
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function editData()
    {
        return [
            [['name'=>'My List11111111','expire'=>'2019-11-19'],'my-api-token',200],
            [['name'=>'My List111111111','expire'=>'2019-11-19'],'',403],
            [['name'=>'jhakjfhkhdskfhkladh','expire'=>'20191111111'],'my-api-token', 400],
        ];
    }

    public function testDeleteUnAuthorizedAction()
    {
        $listId = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name'])->getId();
        $this->client->request(
            'DELETE',
            '/api/checklist/'.$listId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => ''
            ]
        );
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }


    public function testDeleteNotFoundAction()
    {
        $this->client->request(
            'DELETE',
            '/api/checklist/100000',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteAction()
    {
        $list = $this->entityManager->getRepository(CheckList::class)->findOneBy(['name' => 'List Name']);
        $this->client->request(
            'DELETE',
            '/api/checklist/'.$list->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json',
                'HTTP_X-API_KEY' => 'my-api-token'
            ]
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

}
