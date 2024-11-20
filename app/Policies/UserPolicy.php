<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

final class UserPolicy
{
    use HandlesAuthorization;

    public function before (User $user, string $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return ($user->role != User::ROLE_CUSTOMER)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {

        return ($user->role != User::ROLE_CUSTOMER)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return ($user->role != User::ROLE_CUSTOMER)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        return ($user->role != User::ROLE_CUSTOMER)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        return ( $user->role == 55)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function edit(User $user)
    {
        return ( $user->role === 'admin')
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function check_chigit_user(User $user)
    {
        return ($user->role == User::ROLE_STATE_CHIGIT_BOSHLIQ && $user->role == User::ROLE_STATE_CHIGI_XODIM && $user->role == User::ROLE_CITY_CHIGIT)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }



}
