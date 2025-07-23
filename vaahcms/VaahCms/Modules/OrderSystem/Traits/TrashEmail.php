<?php

namespace VaahCms\Modules\OrderSystem\Traits;

use Illuminate\Database\Eloquent\Model;
use VaahCms\Modules\OrderSystem\Mails\TrashMail;
use WebReinvent\VaahCms\Libraries\VaahMail;
use WebReinvent\VaahCms\Models\User;

trait TrashEmail
{


    public static function sendDeleteMail($collection)
    {
        $super_admin = User::whereHas('roles', function ($role) {
            $role->where('name', 'Super Administrator');
        })->first();
        VaahMail::addInQueue(new TrashMail($collection, $super_admin), $super_admin->email);
    }
}
