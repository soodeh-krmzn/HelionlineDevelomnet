<?php

namespace App\Console\Commands;

use App\Models\Admin\Account;
use App\Models\Admin\JobReport;
use App\Models\Person;
use App\Models\Setting;
use App\Services\SMS;
use Illuminate\Console\Command;

class BirthdaySms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:birthday-sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $accounts = Account::whereNotNull('db_name')->whereNotNull('db_user')->whereNotNull('db_pass')->get();
        $month = getMonth();
        $day = getDay();

        foreach ($accounts as $account) {
            try {
                $people_count = 0;
                $sent_count = 0;
                session(['db_name' => $account->db_name]);
                session(['db_user' => $account->db_user]);
                session(['db_pass' => $account->db_pass]);

                $sms = new SMS();
                $setting = new Setting();
                $days = $setting->getSetting('birthday_sms_days');

                if ($days != '') {
                    $people = Person::where('shamsi_birth', 'like', '____/' . $month . '/' . $day + $days)->get();
                    $people_count = $people?->count() ?? 0;
                    foreach ($people as $person) {
                        $sms->send_sms("تولد", $person->mobile, [
                            "name" => $person->name
                        ]);
                        $sent_count++;
                    }

                    JobReport::create([
                        'account_id' => $account->id,
                        'class_name' => get_class($this),
                        'status' => 'success',
                        'error' => null,
                        'details' => $sent_count . ' پیامک از ' . $people_count . ' ارسال شد.'
                    ]);
                }
            } catch (\Exception $e) {
                JobReport::create([
                    'account_id' => $account->id,
                    'class_name' => get_class($this),
                    'status' => 'fail',
                    'error' => $e->getMessage(),
                    'details' => $sent_count . ' پیامک از ' . $people_count . ' ارسال شد.'
                ]);
                continue;
            }
        }
    }
}
