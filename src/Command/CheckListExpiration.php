<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-12
 * Time: 15:21
 */

namespace App\Command;

use App\Entity\CheckList;
use App\Services\MailerService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;

/**
 * Class CheckListExpiration
 * @package App\Command
 * @CronJob("*\/1 * * * *")
 */
class CheckListExpiration extends Command
{
    protected static $defaultName = 'app:scan:checklist';

    private $em;

    private $mailer;

    public function __construct(?string $name = null, ObjectManager $manager, MailerService $mailer)
    {
        $this->mailer = $mailer;
        $this->em = $manager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Scan all lists for expiration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checkLists = $this->em->getRepository(CheckList::class)->findAll();

        foreach ($checkLists as $checkList) {
            $date = new \DateTime($checkList->getExpire());
            if ($date->modify('+1 day')<= new \DateTime('now')) {
                $this->mailer->checkListExpirationOneDayPrior($checkList->getUser(), $checkList);
            }
        }
    }
}
