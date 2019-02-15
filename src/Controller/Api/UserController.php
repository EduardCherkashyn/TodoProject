<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 1/18/19
 * Time: 11:53
 */

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\JsonHttpException;
use App\Model\Card;
use App\Services\CheckListIfExpire;
use App\Services\MailerService;
use App\Services\PasswordEncoder;
use App\Services\UserService;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var PasswordEncoder
     */
    private $encoder;

    public function __construct(SerializerInterface $serializer, UserService $userService, ValidatorInterface $validator, PasswordEncoder $passwordEncoder)
    {
        $this->serializer = $serializer;
        $this->userService = $userService;
        $this->validator = $validator;
        $this->encoder = $passwordEncoder;
    }
    /**
     * @Route("/registration", methods={"POST"})
     */
    public function registrationAction(Request $request, MailerService $mailerService)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        /** @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $this->validator->validate($user);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $this->encoder->encode($user);
        $uuid = Uuid::uuid4();
        $user->setApiToken($uuid->toString());
        $mailerService->registrationEmail($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user);
    }

    /**
     * @Route("/login", methods={"POST"})
     */
    public function authorizeAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, CheckListIfExpire $listIfExpire)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$data['email']]);
        if (!$user instanceof User || !$passwordEncoder->isPasswordValid($user, $data['password'])) {
            throw new JsonHttpException(404, 'Bad Request');
        }
        if (count($user->getCheckLists())>0 && $user->getStripeCustomerId()!=null) {
            $listIfExpire->checkUser($user);
        }

        return $this->json($user);
    }


    /**
     * @Route("/api/user/card", methods={"POST"})
     */
    public function addCardAction(Request $request)
    {
        $json = $request->getContent();
        /** @var Card $card */
        $card = $this->serializer->deserialize($json, Card::class, 'json');
        $violations = $this->validator->validate($card);
        if ($violations->count()) {
            throw new BadRequestHttpException('Invalid Card Data.');
        }
        $this->userService->saveCC($this->getUser(), $card);

        return $this->json([]);
    }
}
