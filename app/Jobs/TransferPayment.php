<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Transfer\Payment as OldPayment;

class TransferPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $errors=[];
        Payment::truncate();
        $oldPayments = OldPayment::all();
        foreach ($oldPayments as $oldPayment) {
            switch ($oldPayment->pa_type) {
                case 'game-factor':
                    $type = 'game';
                    $object_type = 'App\Models\Game';
                    break;
                case 'shop-factor':
                    $type = 'factor';
                    $object_type = 'App\Models\Factor';
                    break;

                default:
                    $type = $oldPayment->pa_type;
                    $object_type = null;
                    break;
            }
            try {
                $payment = Payment::create([
                    'user_id' => $oldPayment->u_id,
                    'person_id' => $oldPayment->p_id,
                    'object_id' => 0,
                    'object_type' => $object_type,
                    'price' => $oldPayment->pa_price,
                    'details' => $oldPayment->pa_details,
                    'type' => $type,
                ]);
                if (DB::connection()->getDatabaseName() != 'helisystem') {
                    DB::table('payments')
                        ->where('id', $payment->id)
                        ->update([
                            'updated_at' => $oldPayment->pa_date,
                            'created_at' => $oldPayment->pa_date
                        ]);
                }
            } catch (\Throwable $th) {
                $errors['payments']=$th->getMessage();
            }
        }
        session('transferError',$errors);
    }
}
