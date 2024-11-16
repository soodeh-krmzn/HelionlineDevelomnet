<?php

namespace App\Console\Commands;

use App\Models\Admin\Account;
use App\Models\Admin\JobReport;
use App\Models\Wallet;
use Illuminate\Console\Command;

class WalletExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wallet-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accounts = Account::whereNotNull('db_name')->whereNotNull('db_user')->whereNotNull('db_pass')->get();
        foreach ($accounts as $account) {
            try {
                $wallets_count = 0;
                $examined_count = 0;
                $unspended_count = 0;
                session(['db_name' => $account->db_name]);
                session(['db_user' => $account->db_user]);
                session(['db_pass' => $account->db_pass]);

                $wallets = Wallet::where('expire', today())->get();
                $wallets_count = $wallets?->count() ?? 0;
                foreach ($wallets as $wallet) {
                    $spend = Wallet::where('person_id', $wallet->person_id)
                            ->where('created_at', '>', $wallet->created_at)
                            ->where('price', '<', 0)
                            ->sum('price');

                    if ($wallet->price > abs($spend)) {
                        $sub = $wallet->price + $spend;
                        $person = $wallet->person;
                        Wallet::create([
                            'person_id' => $person->id,
                            'balance' => $person->wallet_value - $sub,
                            'price' => $sub * (-1),
                            'final_price' => $sub * (-1),
                            'description' => 'انقضاء مبلغ کیف پول کد ' . $wallet->id
                        ]);
                        $person->wallet_value -= $sub;
                        $person->save();

                        $unspended_count++;
                    }
                    $examined_count++;
                }

                JobReport::create([
                    'account_id' => $account->id,
                    'class_name' => get_class($this),
                    'status' => 'success',
                    'error' => null,
                    'details' => 'تعداد ' . $examined_count . ' کیف پول بررسی و انقضاء' . $unspended_count . ' کیف پول ثبت شد.'
                ]);
            } catch (\Exception $e) {
                JobReport::create([
                    'account_id' => $account->id,
                    'class_name' => get_class($this),
                    'status' => 'fail',
                    'error' => $e->getMessage(),
                    'details' => 'تعداد ' . $examined_count . ' کیف پول بررسی و انقضاء' . $unspended_count . ' کیف پول ثبت شد. (تعداد بررسی نشده: ' . $wallets_count - $examined_count . ')'
                ]);
                continue;
            }
        }
    }
}
