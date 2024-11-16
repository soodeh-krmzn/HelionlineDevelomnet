<?php

namespace App\Models;

use App\Models\User;
use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Main
{
    use HasFactory;

    protected $fillable = ['user_id', 'ip', 'url', 'method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showIndex()
    {
        $requests = Request::all();
        if ($requests->count() > 0) {
            ?>
                <table class="table table-bordered">
                    <tr>
                        <td>ردیف</td>
                        <td>نام شخص</td>
                        <td>ip</td>
                        <td>address</td>
                        <td>method</td>
                    </tr>
                <?php
                foreach($requests as $req) {
                ?>
                    <tr>
                        <td><?php echo $req->id ?></td>
                        <td><?php echo $req->user?->getFullName() ?></td>
                        <td><?php echo $req->ip ?></td>
                        <td><?php echo $req->url ?></td>
                        <td><?php echo $req->method ?></td>
                    </tr>
                <?php
                }
                ?>
                </table>
            <?php
            } else {
            ?>
                <div class="row mx-1">
                    <div class="col-12">
                        <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                    </div>
                </div>
            <?php
            }
    }
}
