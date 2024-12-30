<?php

namespace App\Models\Admin;

use App\Models\Admin\Package;
use App\Models\MyModels\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class License extends Admin
{
    use HasFactory, SoftDeletes;

    public static function licenseStatus()
    {
        $licesne = License::where('account_id', auth()->user()->account_id)->first();
        if ($licesne) {
            if ($licesne->status == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function licenseActivate()
    {
        $licesne = License::where('account_id', auth()->user()->account_id)->first();
        if ($licesne) {
            if ($licesne->is_active == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function licenseActivateUser()
    {
        $licesne = License::where('account_id', auth()->user()->account_id)->first();
        if ($licesne) {
            if ($licesne->is_active == 1 && $licesne->user_active != null) {
                return User::where('id', $licesne->user_active)->first();
            } else {
                return false;
            }
        }
    }

    public function showLicense()
    {
        $licenses = License::where('account_id', auth()->user()->account_id)->get();
?>
        <div class="modal-header">
            <h5 class="modal-title" id="licenseModalLabel">لیست لایسنس‌ها و کاربران</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
        </div>
        <div class="modal-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><?= __('ردیف') ?></th>
                        <th><?= __('مجوز عبور') ?></th>
                        <th><?= __('وضعیت') ?></th>
                        <th><?= __('کاربر فعال') ?></th>
                        <th><?= __('وضعیت فعالیت') ?></th>
                    </tr>
                </thead>
                <tbody id="licenseTableBody">
                    <?php foreach ($licenses as $index => $license): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $license->license; ?></td>
                            <td><?php echo $license->status; ?></td>
                            <td><?php
                                $user = User::find($license->user_active);
                                echo $user ? $user->getFullName() : '_';
                                ?>
                            </td>
                            <td><?php echo $license->is_active ? 'در حال استفاده' : 'خاموش'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
        </div>
<?php
    }

    // public function licenseChangeActivate()
    // {
    //     $licesne = License::where('account_id', auth()->user()->account_id)->first();
    //     if ($licesne) {
    //         if ($licesne->is_active == 1) {
    //             $licesne->is_active = 0;
    //             $licesne->user_active = null;
    //         } else {
    //             return false;
    //         }
    //     }
    // }
}
