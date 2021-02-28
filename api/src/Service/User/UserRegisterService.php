<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\User\UserAlreadyExist;
use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use App\Service\Request\RequestService;
use Symfony\Component\HttpFoundation\Request;

class UserRegisterService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var EncoderService
     */
    private EncoderService $encoderService;

    public function __construct(UserRepository $userRepository, EncoderService $encoderService)
    {
        $this->userRepository = $userRepository;
        $this->encoderService = $encoderService;
    }

    public function create(Request $request): User
    {
        $name = RequestService::getFields($request, 'name');
        $email = RequestService::getFields($request, 'email');
        $password = RequestService::getFields($request, 'password');

        $user = new User($name, $email);
        $user->setPassword($this->encoderService->generateEncoderPassword($user, $password));

        try {
            $this->userRepository->save($user);
        } catch (\Exception $exception) {
            throw UserAlreadyExist::fromEmail($email);
        }

        return $user;

    }
}