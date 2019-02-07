<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 1/18/19
 * Time: 11:56
 */

namespace App\Services;

use App\Entity\User;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoder
{
    /**
     * Password encoder.
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function index(User $user)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        try {
            $uuid = Uuid::uuid4();
        } catch (\Exception $e) {
        }
        $user->setApiToken($uuid->toString());
    }
}
