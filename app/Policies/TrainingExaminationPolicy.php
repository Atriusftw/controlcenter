<?php

namespace App\Policies;

use App\TrainingExamination;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingExaminationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the training examination.
     *
     * @param  \App\User  $user
     * @param  \App\TrainingExamination  $examination
     * @return mixed
     */
    public function view(User $user, TrainingExamination $examination)
    {
        return $examination->training->mentors->contains($user) || $user->is($examination->training->user);
    }

    /**
     * Determine whether the user can create training examinations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isMentor();
    }

    /**
     * Determine whether the user can update the training examination.
     *
     * @param  \App\User  $user
     * @param  \App\TrainingExamination  $examination
     * @return mixed
     */
    public function update(User $user, TrainingExamination $examination)
    {
        return $examination->draft ? ($user->isModerator() || $user->is($examination->examiner)) : $user->isModerator();
    }

    /**
     * Determine whether the user can delete the training examination.
     *
     * @param  \App\User  $user
     * @param  \App\TrainingExamination  $trainingExamination
     * @return mixed
     */
    public function delete(User $user, TrainingExamination $trainingExamination)
    {
        return $user->isModerator();
    }
}
