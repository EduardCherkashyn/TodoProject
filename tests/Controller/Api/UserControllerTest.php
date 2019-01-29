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
        $client->request('POST', '/registration',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertContains($data['email'], $client->getResponse()->getContent());

    }

    public function testAuthorizeAction()
    {
        $client = static::createClient();
        $data = [
            'password'=>'123456',
            'email'=>'email@gmail.com'
        ];
        $client->request('POST', '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        /**
         * @var User $user
         */
        $user = $em->getRepository(User::class)->findOneBy(['email'=>$data['email']]);
        $this->assertContains($user->getApiToken(), $client->getResponse()->getContent());
    }
}
