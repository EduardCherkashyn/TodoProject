<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-11
 * Time: 17:02
 */

namespace App\Services;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CheckListIfExpire
{
    private $stripeService;

    private $manager;

    private $mailer;

    public function __construct(StripeService $stripeService, EntityManagerInterface $manager, MailerService $mailer)
    {
        $this->stripeService = $stripeService;
        $this->manager = $manager;
        $this->mailer = $mailer;
    }

    public function check(User $user)
    {
        $countExpiredLists = 0;
        $checkLists = $user->getCheckLists();
        foreach ($checkLists as $checkList) {
            if (date($checkList->getExpire()) < date("Y-m-d")) {
                $this->stripeService->createInvoiceItem($user, $checkList);
                $date = date("Y-m-d");
                $checkList->setExpire(date('Y-m-d', strtotime($date. ' + 5 days')));
                $this->manager->persist($checkList);
                $this->mailer->checkListExpiration($user, $checkList);
                $countExpiredLists++;
            }
        }
        if ($countExpiredLists>0) {
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
}
