<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ApplicationPolicy
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
        return ($user->role != 30 or $user->role != 60)
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
    public function update(User $user, Application $application)
    {
        return ($application->created_by == $user->id && $user->role != User::ROLE_DIROCTOR)
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
    public function myupdate(User $user, Application $application)
    {
        return ($application->status == Application::STATUS_NEW && $application->created_by == $user->id)
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
    public function delete(User $user, Application $application)
    {
        return ($application->created_by == $user->id)
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
    public function accept(User $user, Application $application)
    {
        if($user->role == User::STATE_EMPLOYEE){
            return  $user->state_id == optional($application->organization)->city->state_id
                ? Response::allow()
                : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
        }
        return Response::allow();
           // : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }
    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function send(User $user, Application $application)
    {
        return ( $user->isAdmin() or $user->id == 26)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function mydelete(User $user)
    {
        return ( $user->isAdmin())
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }
}
