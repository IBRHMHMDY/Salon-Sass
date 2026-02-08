<?php

namespace App\Enums;

enum UserRole: string
{
    case DEV_TEST = 'DevTest';
    case SUPER_ADMIN = 'SuperAdmin';
    case OWNER = 'Owner';
    case MANAGER = 'Manager';
    case EMPLOYEE = 'Employee';
    case CUSTOMER = 'Customer';
}
