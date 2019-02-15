<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-11
 * Time: 17:02
 */

namespace App\Services;

use App\Entity\CheckList;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CheckListIfExpire
{
    private $stripeService;

    private $manager;

    private $mailer;

    private $counter = 0;

    public function __construct(StripeService $stripeService, EntityManagerInterface $manager, MailerService $mailer)
    {
        $this->stripeService = $stripeService;
        $this->manager = $manager;
        $this->mailer = $mailer;
    }

    public function checkUser(User $user)
    {
        $checkLists = $user->getCheckLists();
        foreach ($checkLists as $checkList) {
            $this->checkOneListForExpiration($checkList);
        }
        if ($this->counter > 0) {
            $this->createInvoice($user);
        }
    }

    public function checkOneListForExpiration(CheckList $checkList)
    {
        $user = $checkList->getUser();
        if ($checkList->getExpire() < new \DateTime('now')) {
            $this->stripeService->createInvoiceItem($user, $checkList);
            $checkList->setExpire(new \DateTime('now + 10 days'));
            $this->manager->persist($checkList);
            $this->mailer->checkListExpiration($user, $checkList);
            $this->counter++;
        }
    }

    public function createInvoice(User $user)
    {
        $invoice = $this->stripeService->createInvoice($user);
        $userInvoice = new Invoice();
        $userInvoice->setPrice($invoice->amount_due)
                    ->setUser($user)
                    ->setCurrency($invoice->currency)
                    ->setStripeInvoiceId($invoice->id)
                    ->setDescription($invoice->description);
        $this->manager->persist($userInvoice);
        $this->manager->flush();
    }
}
