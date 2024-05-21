<?php

namespace App\EventListener;

use Common\DTO\UserProfileDto;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JWTCreatedListener
{
    protected RequestStack $requestStack;

    protected LoggerInterface $logger;

    public function __construct(
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $username = $payload['username'];

        try {
            /** @var UserProfileDto $userData */
            $userData = json_decode(
                file_get_contents(
                    "http://nginx_app:4045/user/by-username?username=$username",
                ),
            );
        } catch (Exception $exception) {
            $this->logger->error(
                "Error while trying to fetch profile for user with username \"$username\".",
                $exception->getTrace(),
            );
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $exception->getMessage(),
            );
        }

        if (isset($userData)) {
            $payload = array_merge_recursive($payload, (array) $userData);
            if (!$userData->is_active) {
                throw new HttpException(
                    Response::HTTP_FORBIDDEN,
                    'User account has no right to access the requested resource',
                );
            }
        } else {
            throw new HttpException(
                Response::HTTP_UNAUTHORIZED,
                'There is no user profile corresponding to the given credentials',
            );
        }

        $event->setData($payload);
    }
}
