<?php

namespace App\DataFixtures;

use App\Entity\CheckList;
use App\Entity\Item;
use App\Entity\User;
use App\Services\PasswordEncoder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(PasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setPassword('123456');
        $user->setRoles(['ROLE_ADMIN']);
        $this->passwordEncoder->index($user);
        $user->setEmail('email@gmail.com');
        $user->setApiToken('my-api-token');

        $list = new CheckList();
        $list->setName('List Name');
        $list->setExpire('2020-11-20');

        $item = new Item();
        $item->setChecked(true);
        $list->addItem($item);

        $user->addCheckList($list);
        $manager->persist($user);
        $manager->flush();
    }
}
