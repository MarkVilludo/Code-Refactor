<?php
namespace DTApi\Helpers;

use Carbon\Carbon;
use DTApi\Models\Job;
use DTApi\Models\User;
use DTApi\Models\Language;
use DTApi\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeHelper
{
    public static function fetchLanguageFromJobId($id)
    {
        $language = Language::findOrFail($id);
        return $language1 = $language->language;
    }

    public static function getUsermeta($user_id, $key = false)
    {   
        //Get key from user meta model
        $user = UserMeta::where('user_id', $user_id)->first()->$key;
        //if not exist key then do this
        if (!$key) {
            return $user->usermeta()->all();
        } else {
            $meta = $user->usermeta()->where('key', $key)->first();
            if ($meta) {
                return $meta->value;
            } else {
                return '';
            }
        }
    }

    public static function convertJobIdsInObjs($jobs_ids)
    {   
        //Initialize array variable
        $jobs = [];
        foreach ($jobs_ids as $job_obj) {
            $jobs[] = Job::findOrFail($job_obj->id);
        }
        //Return collection of jobs
        return $jobs;
    }

    public static function willExpireAt($due_time, $created_at)
    {
        $due_time = Carbon::parse($due_time);
        $created_at = Carbon::parse($created_at);
        $difference = $due_time->diffInHours($created_at);

        //Additional open and close bracket in condition (s)
        if ($difference <= 90) {
            $time = $due_time;
        } elseif ($difference <= 24) {
            $time = $created_at->addMinutes(90);
        } elseif ($difference > 24 && $difference <= 72) {
            $time = $created_at->addHours(16);
        } else {
            $time = $due_time->subHours(48);
        }

        return $time->format('Y-m-d H:i:s');
    }

}

