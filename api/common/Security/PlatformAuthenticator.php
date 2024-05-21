<?php

namespace Common\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Common\Helper\ApiClientsHelper;

class PlatformAuthenticator extends AbstractGuardAuthenticator
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getAppApiKey()
    {
        return $this->apiKey;
    }

    public function getAppApiClientName()
    {
        return ApiClientsHelper::API_CLIENTS['PARKING_API_CLIENT'];
    }

    /**
     * @inheritDoc
     */
    public function start(
        Request $request,
        AuthenticationException $authException = null
    ): Response {
        return new JsonResponse(
            [
                'message' => 'Authentication is required!,',
            ],
            Response::HTTP_UNAUTHORIZED,
        );
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool
    {
        $authHeader = $request->headers->get('Authorization');
        $apiToken = $request->headers->get('Api-Token');
        $apiClient = $request->headers->get('Api-Client');

        return ($authHeader != null &&
            strtolower(explode(' ', $authHeader)[0]) == 'bearer') ||
            ($apiToken == $this->apiKey &&
                $apiClient != null &&
                ApiClientsHelper::isValidApiClient($apiClient));
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        $authHeader = $request->headers->get('Authorization');
        $apiClient = $request->headers->get('Api-Client');

        if ($authHeader != null) {
            return explode(' ', $authHeader)[1];
        } elseif ($apiClient != null) {
            return $apiClient;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUser(
        $credentials,
        UserProviderInterface $userProvider
    ): ?UserInterface {
        $user = null;

        if ($credentials) {
            $user = $userProvider->loadUserByUsername($credentials);
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ) {
        return new JsonResponse(
            [
                'message' => strtr(
                    $exception->getMessageKey(),
                    $exception->getMessageData(),
                ),
            ],
            Response::HTTP_UNAUTHORIZED,
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $providerKey
    ) {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
