<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-01-29
 * Time: 17:21
 */

namespace App\Tests\Controller\Api;

use App\Tests\AbstractTest;


class UserControllerTest extends AbstractTest
{
    /**
     * @test
     * @dataProvider userData
     */
    public function testRegistrationAction($a, $b, $expected): void
    {
        $data = [
            'password'=> $a,
            'email'=> $b
            ];
        $this->client->request(
            'POST',
            '/registration',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function userData()
    {
        return [
             ['123456', 'edik22@ukr.net', 200],
             ['1111', 'ed@ukr.net', 400],
             ['222222222222222', 'ededdedededede@ilr.met', 400],
             ['', '', 400]
        ];
    }

    /**
     * @dataProvider userDataLogin
     * @test
     */
    public function testAuthorizeAction($a, $b, $expected) :void
    {
        $data = [
            'password'=> $a,
            'email'=> $b
        ];
        $this->client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertEquals($expected, $this->client->getResponse()->getStatusCode());
    }

    public function userDataLogin()
    {
        return [
            ['123456', 'email@gmail.com', 200],
            ['1111', 'ed@ukr.net', 404],
            ['222222222222222', 'ededdedededede@ilr.met', 404],
            ['', '', 404]
        ];
    }
}
