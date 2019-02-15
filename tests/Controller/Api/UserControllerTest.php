<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-01-29
 * Time: 17:21
 */

namespace App\Tests\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegistrationAction()
    {
        $client = static::createClient();
        $data = [
            'password'=>'123456',
            'email'=>time().'@ukr.net'
            ];
        $client->request(
            'POST',
            '/registration',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertContains($data['email'], $client->getResponse()->getContent());
    }

    public function testRegistrationBadRequestAction()
    {
        $client = static::createClient();
        $data = [
            'password'=>'12',
            'email'=>time().'@ukr.net'
        ];
        $client->request(
            'POST',
            '/registration',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testAuthorizeBadRequestAction()
    {
        $client = static::createClient();
        $data = [
            'password'=>'11111',
            'email'=>'edik@111.mail.com'
        ];
        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

    }

    public function testAuthorizeAction()
    {
        $client = static::createClient();
        $data = [
            'password'=>'123456',
            'email'=>'email@gmail.com'
        ];
        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
}
