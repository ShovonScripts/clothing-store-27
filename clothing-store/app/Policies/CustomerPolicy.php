<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_customers');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->can('view_customers');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can('manage_customers');
    }

    public function blacklist(User $user): bool
    {
        return $user->can('blacklist_customers');
    }
}
