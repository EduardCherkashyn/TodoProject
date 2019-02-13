<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-12
 * Time: 12:29
 */

namespace App\Services;

use App\Entity\CheckList;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerService extends AbstractController
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function registrationEmail(User $user)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('edikparker@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'email/registration.html.twig',
                    ['name' => $user->getUsername()]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    public function checkListExpiration(User $user, CheckList $checkList)
    {
        $message = (new \Swift_Message('Checklist expiration!'))
            ->setFrom('edikparker@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'email/checklistExpiration.html.twig',
                    ['name' => $user->getUsername(),
                     'checklist'=> $checkList ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    public function checkListExpirationOneDayPrior(User $user, CheckList $checkList)
    {
        $message = (new \Swift_Message('Checklist expiration!'))
            ->setFrom('edikparker@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'email/checklistExpirationOneDayPrior.html.twig',
                    ['name' => $user->getUsername(),
                        'checklist'=> $checkList ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
