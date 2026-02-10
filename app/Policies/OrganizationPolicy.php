<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\DefaultModels\tbl_activities as DefaultModelsTbl_activities;
use App\Models\OrganizationCompanies;
use App\Models\User;
use App\tbl_activities;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
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
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OrganizationCompanies  $company
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user,OrganizationCompanies $company)
    {
        if($user->role == User::ROLE_CUSTOMER){
            return DefaultModelsTbl_activities::where('action_id','=',$company->id)
                ->where('action_type','=','organization_add')
                ->where('user_id','=',$user->id)
                ->first() ? Response::allow()
                : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
        }elseif($user->role == User::STATE_EMPLOYEE){
            return ($company->city->state_id == $user->state_id)
                ? Response::allow()
                : Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
        }elseif($user->role == User::ROLE_DIROCTOR && $user->id != 137){
            return Response::deny('Sizga ushbu sahifadan foydalanishga ruxsat berilmagan.');
        }
        return Response::allow();
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
