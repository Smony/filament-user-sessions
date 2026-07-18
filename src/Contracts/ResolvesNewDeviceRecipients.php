<?php

namespace Smony\FilamentUserSessions\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface ResolvesNewDeviceRecipients
{
    /**
     * Who should be notified that $user just logged in from a new device.
     *
     * @return iterable<int, Authenticatable>
     */
    public function resolve(Authenticatable $user): iterable;
}
