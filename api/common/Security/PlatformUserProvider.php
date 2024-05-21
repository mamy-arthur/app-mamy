<?php


namespace Common\Security;


use Common\DTO\PlatformUserDto;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Common\Helper\ApiClientsHelper;

class PlatformUserProvider implements UserProviderInterface
{
    protected RequestStack $requestStack;
    protected LoggerInterface $logger;

    public function __construct(RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username)
    {
        $authHeader = $this->requestStack->getCurrentRequest()->headers->get('Authorization');
        $apiClient = $this->requestStack->getCurrentRequest()->headers->get('Api-Client');
        $data = [];

        if($authHeader != null){
            $context = stream_context_create([
                'http' => [
                    'header' => "Authorization: $authHeader\r\n" .
                        "Content-Type: application/json\r\n",
                ],
            ]);
    
            try {
                $data = json_decode(file_get_contents('http://nginx_app:4041/user', false, $context), true);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
    
                throw new UnauthorizedHttpException('Could not load current user.');
            }

        }
        
        elseif($apiClient != null ){
            $data = ["username" => $apiClient, "roles" => ApiClientsHelper::getRolesForApiClient($apiClient) ];
        }

        return !empty($data) ? new PlatformUserDto($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        throw new Exception('TODO: fill in refreshUser() inside ' . __FILE__);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return PlatformUserDto::class === $class || is_subclass_of($class, PlatformUserDto::class);
    }
}
