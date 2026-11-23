<?php

declare(strict_types=1);

namespace App\Enums;

enum NavigationGroup: string
{
    case Orders = 'Orders';
    case Customers = 'Customers';
    case Catalog = 'Catalog';
    case Marketing = 'Marketing';
    case Content = 'Content';
    case Delivery = 'Delivery';
    case Settings = 'Settings';
}
