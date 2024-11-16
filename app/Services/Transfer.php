<?php

namespace App\Services;

use PDO;
use SoapClient;
use Carbon\Carbon;

use App\Models\Cost;

use App\Models\Payment;
use App\Jobs\TransferPayment;

use App\Models\Game as Game2;
use App\Models\Transfer\Game;

use App\Models\Transfer\Offer;
use Hekmatinasser\Verta\Verta;

use App\Models\Offer as Offer2;
use App\Models\Transfer\Factor;

use App\Models\Transfer\Person;
use App\Models\Transfer\Package;

use App\Models\Transfer\Product;
use App\Models\Transfer\Section;

use App\Models\Factor as Factor2;
use App\Models\Person as Person2;

use Illuminate\Support\Facades\DB;
use App\Models\Package as Package2;
use App\Models\Product as Product2;
use App\Models\Section as Section2;
use App\Models\Transfer\PersonMeta;
use Illuminate\Support\Facades\Auth;
use App\Models\Category as Category2;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Models\Transfer\Cost as OldCost;
use App\Models\Transfer\ProductCategory;
use App\Models\PersonMeta as PersonMeta2;
use App\Models\Transfer\Payment as OldPayment;
use App\Models\Transfer\Wallet as OldWallet;
use App\Models\Wallet;

class Transfer
{
    public $errors = [];
    public function convertDate()
    {
        $list = Person2::all();
        $i = 1;
        foreach ($list as $l) {
            if ($l->expire != null) {
                $ex = str_replace('/', '-', $l->expire);
                $expire = Verta::parse($ex)->formatGregorian('Y-m-d');
            } else {
                $expire = null;
            }
            $l->update([
                'expire' => $expire
            ]);
            $i++;
        }
    }

    public function truncateTables()
    {
        dump(DB::connection()->getDatabaseName());

        $this->connectToMine();
        if (DB::connection()->getDatabaseName() == auth()->user()->account_id . "db") {
            $this->truncateAll();
        }
        dd('truncate Tables for ' . DB::connection()->getDatabaseName());
    }

    public function dry_run()
    {
?>
        <pre>
        <?php
        ini_set('max_execution_time', 1000);

        echo "Section2: <br>";
        $sections = Section::all();
        foreach ($sections as $section) {
            try {
                Section2::create([
                    'id' => $section->sec_id,
                    'name' => $section->sec_name,
                    'show_status' => 1,
                    'details' => ''
                ]);
            } catch (\Throwable $th) {
                $this->errors['Section-' . count($sections)][] = $th->getMessage();
            }
        }
        echo "<hr>";

        $offers = Offer::all();
        echo "Offer: <br>";

        foreach ($offers as $offer) {
            try {
                $i = Offer2::create([
                    'id' => $offer->o_id,
                    'name' => $offer->o_code,
                    'type' => $offer->o_type,
                    'per' => $offer->o_per,
                    'min_price' => $offer->min_price,
                    'calc' => 'all',
                    'details' => $offer->o_details,
                    'times_used' => 0
                ]);
            } catch (\Throwable $th) {
                $this->errors['Offer-' . count($offers)][] = $th->getMessage();
            }
        }

        /* o_id, o_code, o_type, o_per, o_details, min_price */

        echo "<hr>";

        echo "Package: <br>";

        $packages = Package::all();

        foreach ($packages as $package) {
            try {
                Package2::create([
                    'id' => $package->pk_id,
                    'name' => $package->pk_name,
                    'price' => $package->pk_price,
                    'expire_time' => $package->pk_time,
                    'expire_day' => $package->pk_expire
                ]);
            } catch (\Throwable $th) {
                $this->errors['Package-' . count($packages)][] = $th->getMessage();
            }
        }

        /* pk_id, pk_name, pk_price, pk_time, pk_expire */

        echo "<hr>";

        $categories = ProductCategory::all();
        echo "Category: <br>";
        try {
            foreach ($categories as $category) {
                Category2::create([
                    'id' => $category->cat_id,
                    'name' => $category->cat_name,
                    'parent_id' => $category->cat_parent,
                    'order' => $category->cat_order,
                    'status' => $category->cat_status,
                    'details' => $category->cat_details,
                    'icon' => '',
                    'image' => ''
                ]);
            }
        } catch (\Throwable $th) {
            $this->errors['Category-' . count($categories)][] = $th->getMessage();
        }

        /* cat_id, cat_name, cat_parent, cat_order, cat_img, cat_details, cat_status */

        echo "<hr>";

        $products = Product::all();
        echo "Product: <br>";
        try {
            foreach ($products as $product) {
                Product2::create([
                    'id' => $product->pr_id,
                    'name' => $product->pr_name,
                    'stock' => $product->pr_stock,
                    'buy' => $product->pr_buy,
                    'sale' => $product->pr_sale,
                    'cart' => 0,
                    'image' => $product->pr_image,
                    'status' => $product->pr_status
                ]);
            }
        } catch (\Throwable $th) {
            $this->errors['Product-' . count($products)][] = $th->getMessage();
        }

        /* pr_id, pr_name, pr_stock, pr_buy, pr_sale, pr_cat, pr_image, pr_status */

        echo "<hr>";

        $persons = Person::all();
        echo "Person: <br>";

        foreach ($persons as $person) {
            if ($person->p_birth != null) {
                try {
                    $birth = Verta::parse($person->p_birth)->formatGregorian('Y-m-d');
                    $shamsi=str_replace('-','/',$person->p_birth);
                } catch (\Throwable $th) {
                    $birth = null;
                    $shamsi=null;
                }
            } else {
                $birth = null;
            }
            try {
                Person2::create([
                    'id' => $person->p_id,
                    'created_by' => $person->u_id,
                    'name' => $person->p_name,
                    'family' => $person->p_family,
                    'fname' => $person->p_fname,
                    'birth' => $birth,
                    'shamsi_birth' => $shamsi,
                    'address' => '',
                    'card_code' => $person->p_code,
                    'gender' => $person->p_gender,
                    'mobile' => $person->p_mobile,
                    'sharj' => $person->p_sharj,
                    'expire' => $person->p_expire,
                    'pack' => $person->p_pack,
                    'commitment' => $person->p_commitment,
                    'profile' => $person->p_profile,
                    'club' => 0,
                    'rate' => $person->p_rate,
                    'reg_code' => 0,
                    'national_code' => '',
                    'wallet_value' => 0,
                    'balance' => 0
                ]);
            } catch (\Throwable $th) {
                $this->errors['Person-' . count($persons)][] = $th->getMessage();
            }
        }

        /*
        p_id, u_id, p_name, p_family, p_fname, p_birth, p_code, p_gender, p_mobile, p_sharj, p_expire, p_pack, p_regdate, p_commitment, p_profile, p_rate
        */

        echo "<hr>";
        $personMetaes = PersonMeta::all();
        echo "Person Meta: <br>";
        try {
            foreach ($personMetaes as $personMeta) {
                PersonMeta2::create([
                    'id' => $personMeta->pm_id,
                    'p_id' => $personMeta->p_id,
                    'meta' => $personMeta->pm_meta,
                    'value' => $personMeta->pm_value
                ]);
            }
        } catch (\Throwable $th) {
            $this->errors['PersonMeta-' . count($personMetaes)][] = $th->getMessage();
        }

        /* pm_id, p_id, pm_meta, pm_value */

        echo "<hr>";

        // $factors = Factor::all();
        // echo "Factor: <br>";
        // foreach ($factors as $factor) {
        /*
            echo
            $factor->f_id
                . ' => ' .
            $factor->u_id
                . ' => ' .
	        $factor->p_id
                . ' => ' .
            $factor->pr_id
                . ' => ' .
            $factor->f_count
                . ' => ' .
            $factor->pr_price
                . ' => ' .
            $factor->f_date
                . ' => ' .
            $factor->f_status
                . ' => ' .
            $factor->f_cook_user
                . ' => ' .
            $factor->f_cook_update
                . ' => ' .
            $factor->f_cook_status;
            echo "<br>";
            */
        // }
        /*
        f_id, u_id, p_id, pr_id, f_count, pr_price, f_date, f_status, f_cook_user, f_cook_update, f_cook_status
        */
        // echo "<hr>";
        // $games = Game::all();
        // echo "Game: <br>";
        // foreach ($games as $game) {
        //     try {
        //         $game2 = Game2::create([
        //             'id' => $game->g_id,
        //             'user_id' => $game->u_id,
        //             'person_id' => $game->p_id,
        //             'type' => null,
        //             'count' => $game->g_count,
        //             'in_time' => $game->g_in,
        //             'out_time' => $game->g_out,
        //             'in_date' => $game->g_date,
        //             'out_date' => $game->g_date,
        //             'total' => $game->g_total,
        //             'total_vip' => $game->g_total_vip,
        //             'extra' => $game->g_extra,
        //             'total_price' => $game->g_total_price + $game->g_total_shop, //###
        //             'total_vip_price' => $game->g_total_vip_price,
        //             'extra_price' => $game->g_extra_price,
        //             'used_sharj' => $game->g_used_sharj,
        //             'login_price' => $game->g_login_price,
        //             'game_price' => $game->g_total_price,
        //             'total_shop' => $game->g_total_shop,
        //             'final_price' => ($game->g_total_price + $game->g_total_shop) - $game->g_offer_price,
        //             'offer_code' => $game->g_offer_code,
        //             'offer_price' => $game->g_offer_price,
        //             'offer_name' => '',
        //             'offer_calc' => '',
        //             'updated_by' => '',
        //             'status' => $game->g_status,
        //             'adjective' => $game->g_adjective,
        //             'enter_type' => $game->enter_type,
        //             'person_fullname' => '',
        //             'person_mobile' => '',
        //             'section_id' => $game->g_type,
        //             'section_name' => '',
        //             'station_id' => '',
        //             'station_name' => '',
        //             'counter_id' => '',
        //             'counter_name' => '',
        //             'counter_min' => '',
        //             'counter_passed' => '',
        //             'deposit' => $game->pre_pay_price ?? 0,
        //             'deposit_type' => $game->pre_pay_type,
        //             'accompany_name' => $game->accomp_name,
        //             'accompany_mobile' => $game->accomp_mobile,
        //             'accompany_relation' => $game->accomp_rel,
        //             'group_id' => ''
        //         ]);
        //         if (DB::connection()->getDatabaseName() != 'helisystem') {
        //             DB::table('games')
        //                 ->where('id', $game2->id)
        //                 ->update([
        //                     'updated_at' => $game->g_date,
        //                     'created_at' => $game->g_date
        //                 ]);
        //         }
        //     } catch (\Throwable $th) {
        //         $this->errors['Game-' . count($games)][] = $th->getMessage();
        //     }
        // }

        //============================================
        //report section: cost - payment

        echo "Cost: <br>";
        $oldCosts = OldCost::all();
        foreach ($oldCosts as $oldCost) {
            try {
                $cost = Cost::create([
                    'id' => $oldCost->c_id,
                    'created_by' => 0,
                    'price' => $oldCost->c_price,
                    'details' => $oldCost->c_details
                ]);
                DB::table('costs')
                    ->where('id', $cost->id)
                    ->update([
                        'updated_at' => $oldCost->c_date,
                        'created_at' => $oldCost->c_date
                    ]);
            } catch (\Throwable $th) {
                $this->errors['Cost-' . count($oldCosts)][] = $th->getMessage();
            }
        }
        //============================================
        // echo "Wallets: <br>";
        // $oldWallets = OldWallet::all();
        // foreach ($oldWallets as $oldWallet) {
        //     Wallet::create([
        //         'person_id' => $oldWallet->p_id,
        //         'balance' => $oldWallet->ballance,
        //         'price' => $oldWallet->price,
        //         'final_price' => $oldWallet->price,
        //     ]);
        //     try {
        //         if ($oldWallet->ballance > 0) {
        //             Person2::findOrFail($oldWallet->p_id)->update([
        //                 'wallet_value' => $oldWallet->ballance,
        //             ]);
        //         }
        //     } catch (\Throwable $th) {
        //        $this->errors['wallet-p'][$oldWallet->p_id]=$th->getMessage();
        //     }
        // }
        ?>
        </pre>
<?php
        dd($this->errors);
        return 'done';
    }
    public function paymentAll()
    {
        Payment::truncate();
        $oldPayments = OldPayment::all();
        $i = 0;
        $key = 1;
        foreach ($oldPayments as $oldPayment) {
            // if ($key/5000==0) {
            //     $i++;
            //     session('counter',$i);
            //    sleep(1);
            // }
            // $key++;
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
                $this->errors['payment'][] = $th->getMessage();
            }
        }
    }


    //================================================
    //================================================
    public function connectToMine()
    {
        $account = auth()->user()->account;
        $db = new Database($account->db_name, $account->db_user, $account->db_pass);
        $db->connect();
    }

    public function truncateAll()
    {
        // Truncate all tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $tables = [];

        $databaseName = auth()->user()->account_id . 'db';
        $connection = DB::connection()->getPdo();

        $statement = $connection->prepare("SHOW TABLES FROM `$databaseName`");
        $statement->execute();

        while ($row = $statement->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        foreach ($tables as $table_name) {
            if (in_array($table_name, ['settings', 'payment_types'])) {
                continue;
            }
            DB::table($table_name)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
