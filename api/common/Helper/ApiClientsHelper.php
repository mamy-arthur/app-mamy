<?php

namespace Common\Helper;

class ApiClientsHelper
{
    const API_CLIENTS = [
        'AUTH_API_CLIENT' => 'Auth_Api_Client',
        'USERS_API_CLIENT' => 'Users_Api_Client',
        'PARKING_API_CLIENT' => 'Parking_Api_Client',
        'AIR_TRANSPORT_API_CLIENT' => 'Air_Transport_Api_Client',
        'ORDERS_API_CLIENT' => 'Orders_Api_Client',
        'CLIENTS_API_CLIENT' => 'Clients_Api_Client',
        'MESSAGING_API_CLIENT' => 'Messaging_Api_Client',
        'FLIGHTS_API_CLIENT' => 'Flights_Api_Client',
        'BILLING_API_CLIENT' => 'Billing_Api_Client',
    ];

    static function isValidApiClient($client): bool{
        return in_array($client, static::API_CLIENTS);
    }

    const API_CLIENTS_ROLES = [
        'Auth_Api_Client'=> ['SYSTEM'],
        'Users_Api_Client' => ['SYSTEM'],
        'Parking_Api_Client' => ['SYSTEM'],
        'Air_Transport_Api_Client' => ['SYSTEM'],
        'Orders_Api_Client' => ['SYSTEM'],
        'Clients_Api_Client' => ['SYSTEM'],
        'Messaging_Api_Client' => ['SYSTEM'],
        'Flights_Api_Client' => ['SYSTEM'],
        'Billing_Api_Client' => ['SYSTEM'],
    ];

    static function getRolesForApiClient($client){
        return static::API_CLIENTS_ROLES[$client];
    } 
}
