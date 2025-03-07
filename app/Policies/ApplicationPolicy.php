<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Redirect;

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
     * @param User $user
     * @return Response|bool
     * @throws AuthorizationException
     */
    public function viewAny(User $user)
    {
        if ($user->role == User::ROLE_CUSTOMER) {
            throw new AuthorizationException(trans('app.You Are Not Authorize This page.'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @return Response|bool
     * @throws AuthorizationException
     */
    public function view(User $user)
    {

        if ($user->role == User::ROLE_CUSTOMER) {
            throw new AuthorizationException(trans('app.You Are Not Authorize This page.'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     * @throws AuthorizationException
     */
    public function create(User $user)
    {
        if ($user->role != User::LABORATORY_DIRECTOR or $user->role != User::STATE_EMPLOYEE) {
            throw new AuthorizationException(trans('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @param Application $application
     * @return Response|bool
     * @throws AuthorizationException
     */
    public function edit(User $user,Application $application)
    {
        if ($application->created_by != $user->id or $application->status != Application::STATUS_FINISHED) {
            throw new AuthorizationException(trans('app.Ushbu arizani o\'zgartirish huquqi sizda mavjud emas.'));
        }

        return Response::allow();
    }
    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function sertificateCreate(User $user)
    {
        return (false)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Application $application
     * @return Response|bool
     * @throws AuthorizationException
     */
    public function update(User $user, Application $application)
    {
        if ($application->created_by != $user->id or $application->status != Application::STATUS_NEW) {
            throw new AuthorizationException(trans('app.Ushbu arizani o\'zgartirish huquqi sizda mavjud emas.'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Application $application
     * @return Response|bool
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
     * @param User $user
     * @param Application $application
     * @return Response|bool
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
     * @param User $user
     * @param Application $application
     * @return Response|bool
     */
    public function send(User $user, Application $application)
    {
        return ( $user->isAdmin() or $user->id == 27)
            ? Response::allow()
            : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
    }

}
