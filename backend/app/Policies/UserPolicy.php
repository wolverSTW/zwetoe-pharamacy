<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * User စာရင်း တစ်ခုလုံးကို ကြည့်ရှုခွင့် (Sidebar မှာ ပေါ်မပေါ်)
     */
    public function viewAny(User $user): bool
    {
        // Admin သာလျှင် User Management menu ကို မြင်ရမည်
        return $user->isAdmin();
    }

    /**
     * User တစ်ယောက်ချင်းစီ၏ အသေးစိတ်ကို ကြည့်ရှုခွင့်
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * User အသစ် ဆောက်လုပ်ခွင့်
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * User အချက်အလက် ပြင်ဆင်ခွင့်
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * User ဖျက်ဆီးခွင့်
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}