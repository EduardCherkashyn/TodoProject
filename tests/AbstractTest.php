<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-17
 * Time: 18:24
 */

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractTest extends WebTestCase
{
    /** @var Client $client */
    protected $client;
    /** @var EntityManagerInterface */
    protected $entityManager;


    protected function setUp()
    {
        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown()
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->rollback();
        }
    }


}