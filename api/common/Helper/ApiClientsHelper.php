<?php

namespace Common\Helper;

class ApiClientsHelper
{
    const API_CLIENTS = [
        'AUTH_API_CLIENT' => 'Auth_Api_Client',
        'USERS_API_CLIENT' => 'Users_Api_Client',
        'MESSAGING_API_CLIENT' => 'Messaging_Api_Client',
    ];

    static function isValidApiClient($client): bool{
        return in_array($client, static::API_CLIENTS);
    }

    const API_CLIENTS_ROLES = [
        'Auth_Api_Client'=> ['SYSTEM'],
        'Users_Api_Client' => ['SYSTEM'],
        'Messaging_Api_Client' => ['SYSTEM'],
    ];

    static function getRolesForApiClient($client){
        return static::API_CLIENTS_ROLES[$client];
    } 
}
