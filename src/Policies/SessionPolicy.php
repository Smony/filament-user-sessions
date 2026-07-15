<?php

namespace Smony\FilamentUserSessions\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Smony\FilamentUserSessions\Models\Session;

class SessionPolicy
{
    /**
     * Whether the given user can see the sessions list and the "online now" widget.
     */
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, Session $session): bool
    {
        return true;
    }

    /**
     * Whether the given user can revoke the given session.
     */
    public function delete(Authenticatable $user, Session $session): bool
    {
        return true;
    }

    /**
     * Whether the given user can bulk-revoke sessions.
     */
    public function deleteAny(Authenticatable $user): bool
    {
        return true;
    }
}
