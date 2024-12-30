<?php

namespace App\Models\Admin;

use App\Models\Admin\Package;
use App\Models\MyModels\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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
            <h5 class="modal-title" id="licenseModalLabel">مجوز های عبور</h5>
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
                            <td>
                                <span title="<?php echo $license->license ?>">
                                    <?php echo Str::limit($license->license, 10, '...') ?>
                                </span>
                            </td>
                            <td class="<?php echo $license->is_active == 0 ? 'text-danger' : 'text-success'; ?>">
                                <?php echo $license->status == 0 ? "غیر فعال" : "فعال"; ?></td>
                            <td>
                                <?php
                                $user = User::find($license->user_active);
                                if ($user) {
                                    echo '<span class="badge bg-slack rounded-pill p-1"><i class="bi bi-person"></i> ' . $user->getFullName() . '</span>';
                                } else {
                                    echo '_';
                                }
                                ?>
                            </td>
                            <td class="<?php echo $license->is_active == 0 ? 'text-secondry' : 'text-success'; ?>">
                                <?php echo $license->is_active == 0 ? 'خاموش' : 'در حال استفاده'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
