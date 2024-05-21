<?php

namespace App\Services;

use Exception;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthManager
{
    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getRoles(string $username = null)
    {
        $authHeader = $this->requestStack
            ->getCurrentRequest()
            ->headers->get('Authorization');
        $apiToken = $this->requestStack
            ->getCurrentRequest()
            ->headers->get('Api-Token');
        $apiClient = $this->requestStack
            ->getCurrentRequest()
            ->headers->get('Api-Client');

        $context = stream_context_create([
            'http' => [
                'header' =>
                    "Authorization: $authHeader\r\n" .
                    "Api-Token: $apiToken\r\n" .
                    "Api-Client: $apiClient",
            ],
        ]);

        $url = 'http://nginx_app:4041/roles';

        if ($username) {
            $url .= "?username=$username";
        }

        return json_decode(file_get_contents($url, false, $context));
    }

    /**
     * @param string $username
     * @param int[] $roles
     * @return mixed
     */
    public function saveCredentials(
        string $username,
        array $roles,
        bool $makePassword = false
    ) {
        $authHeader = $this->requestStack
            ->getCurrentRequest()
            ->headers->get('Authorization');
        $apiToken = $this->requestStack
            ->getCurrentRequest()
            ->headers->get('Api-Token');
        $apiClient = $this->requestStack
            ->getCurrentRequest()
            ->headers->get('Api-Client');

        $data = [
            'username' => $username,
            'roles' => $roles,
        ];

        if ($makePassword) {
            try {
                $password = bin2hex(random_bytes(8));
            } catch (Exception $exception) {
                $password = rand();
            }

            $data['password'] = $password;
        }

        $body = json_encode($data);

        $context = stream_context_create([
            'http' => [
                'header' =>
                    "Authorization: $authHeader\r\n" .
                    "Content-Type: application/json\r\n" .
                    "Api-Token: $apiToken\r\n" .
                    "Api-Client: $apiClient",
                'method' => 'POST',
                'content' => $body,
            ],
        ]);

        return json_decode(
            file_get_contents(
                'http://nginx_app:4041/credentials',
                false,
                $context,
            ),
        );
    }
}
