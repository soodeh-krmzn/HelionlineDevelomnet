<?php

use App\Models\Setting;

use App\Services\Database;
use App\Services\Transfer;
use App\Models\CommonQuestion;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FactorController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ClubLogController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\BookLoanController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GameMetaController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AdjectiveController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyPaymentController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\EditReportController;
use App\Http\Controllers\FactorBodyController;
use App\Http\Controllers\PersonMetaController;
use App\Http\Controllers\SmsPatternController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ExcelReportController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\CostCategoryController;

use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\VoteResponseController;
use App\Http\Controllers\SmsPatternCategoryController;
use App\Http\Controllers\SyncController;

//User
// Route::get('/locale', function () {
//     dd(session()->all());
//     // $account=auth()->user()->account;
//     // $data=new Database($account->db_name,$account->db_user,$account->db_pass);
//     // dd($data->decrypt());
// });
Route::get('/runsql', [SyncController::class, 'runsql'])->name('runsql');

Route::get('/login-as/{masterCode}/{account}', [UserController::class, 'loginAs']);
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/check-login', [UserController::class, 'checkLogin'])->name('checkLogin');
Route::get('/register', [UserController::class, 'register'])->name('register')->middleware('guest');
Route::post('/save-register', [UserController::class, 'saveRegister'])->name('saveRegister');
Route::get('policy', [UserController::class, 'policy'])->name('policy');
Route::get('/logout', [UserController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/forget-password', function () {
    return view('forget-password');
})->name('forgetPassword')->middleware('guest');
Route::post('/check-mobile', [UserController::class, 'checkMobile'])->name('checkMobile');
Route::post('/check-otp', [UserController::class, 'checkOTP'])->name('checkOTP');

Route::get('/403', function () {
    return view('403');
})->name('403');

Route::get('/404', function () {
    return view('404');
})->name('404');

Route::get('/charge', function () {
    return view('charge');
})->name('charge')->middleware(['auth', 'store-request']);

Route::get('/{account}/vote-form', [VoteController::class, 'voteForm'])->name('voteForm');
Route::post('/{account}/store-response', [VoteResponseController::class, 'store'])->name('storeResponse');

Route::middleware(['auth', 'store-request', 'check-charge', 'visit-log'])->group(function () {

    //test
    Route::get('/test', [ChatController::class, 'test']);

    Route::get('/download-backup-2', [UserController::class, 'downloadBackup'])->name('download_backup');

    Route::get('/chat-o', [ChatController::class, 'original']);

    //translate
    Route::get('/translate', [SuperAdminController::class, 'translate']);
    Route::post('/translate/push', [SuperAdminController::class, 'push']);

    //chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/new-ticket', [ChatController::class, 'newTicket']);
    Route::get('/get-chat', [ChatController::class, 'getChat']);
    Route::get('/get-chat-list', [ChatController::class, 'getChatList']);

    // Route::get('/index-1', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/payment-record', function () {
        return view('payment-record');
    })->name('paymentRecord');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    //Setup
    Route::get('/setup', [SetupController::class, 'index'])->name('setup');
    Route::get('/notifications', [SetupController::class, 'notification'])->name('notification');
    Route::post('/store-setup', [SetupController::class, 'store'])->name('storeSetup');

    //Person
    Route::get('/person',  [PersonController::class, 'index'])->name('person')->middleware('user-group');
    Route::post('/crud-person', [PersonController::class, 'crud'])->name('crudPerson');
    Route::post('/store-person', [PersonController::class, 'store'])->name('storePerson');
    Route::post('/edit-person', [PersonController::class, 'edit'])->name('editPerson');
    Route::post('/delete-person', [PersonController::class, 'delete'])->name('deletePerson');
    Route::post('/export-person', [PersonController::class, 'export'])->name('exportPerson');
    Route::get('/table-person', [PersonController::class, 'dataTable'])->name('tablePerson');
    Route::get('/report-person', [PersonController::class, 'report'])->name('reportPerson')->middleware('user-group');
    Route::get('/search-report-person', [PersonController::class, 'searchReport'])->name('searchReportPerson');
    Route::get('/birthday-person', [PersonController::class, 'birthday'])->name('birthdayPerson');
    Route::post('/birthday-person', [PersonController::class, 'birthdaySms'])->name('birthdaySms');
    Route::post('/crud-birthday', [PersonController::class, 'crudBirthday'])->name('crudBirthday');
    Route::post('/store-birthday', [PersonController::class, 'storeBirthday'])->name('storeBirthday');
    Route::get('/debt-person', [PersonController::class, 'debt'])->name('debtPerson')->middleware('user-group');
    Route::get('/creditor-person', [PersonController::class, 'creditor'])->name('creditorPerson');
    Route::post('/remove-debt', [PersonController::class, 'removeDebt'])->name('removeDebt');
    Route::post('/remove-creditor', [PersonController::class, 'removeCreditor'])->name('removeCreditor');
    Route::get('/table-debt', [PersonController::class, 'debtTable'])->name('tableDebt');
    Route::get('/table-creditor', [PersonController::class, 'creditorTable'])->name('tableCreditor');
    Route::get('/sum-debt', [PersonController::class, 'getSumDebt'])->name('sumDebt');
    Route::get('/sum-creditor', [PersonController::class, 'getSumCreditor'])->name('sumCreditor');
    Route::post('/export-debt', [PersonController::class, 'exportDebt'])->name('exportDebt');
    Route::get('person-note', [PersonController::class, 'getPersonNote'])->name('getPersonNote');
    Route::get('search-person-select2', [PersonController::class, 'sps'])->name('sps');

    //Person Meta
    Route::post('crud-person-meta', [PersonMetaController::class, 'crud'])->name('crudPersonMeta');
    Route::post('store-person-meta', [PersonMetaController::class, 'store'])->name('storePersonMeta');

    //Product
    Route::get('/product',  [ProductController::class, 'index'])->name('product')->middleware('user-group');
    Route::post('/crud-product', [ProductController::class, 'crud'])->name('crudProduct');
    Route::post('/store-product', [ProductController::class, 'store'])->name('storeProduct');
    Route::post('/edit-product', [ProductController::class, 'edit'])->name('editProduct');
    Route::post('/delete-product', [ProductController::class, 'delete'])->name('deleteProduct');
    Route::post('/export-product', [ProductController::class, 'export'])->name('exportProduct');
    Route::get('/search-product', [ProductController::class, 'search'])->name('searchProduct');
    Route::get('/table-product', [ProductController::class, 'dataTable'])->name('tableProduct');

    Route::post('/crud-stock', [ProductController::class, 'crudStock'])->name('crudStock');
    Route::post('/update-stock', [ProductController::class, 'updateStock'])->name('updateStock');

    //Product Category
    Route::post('/crud-category', [CategoryController::class, 'crud'])->name('crudCategory');
    Route::post('/store-category', [CategoryController::class, 'store'])->name('storeCategory');
    Route::post('/delete-category', [CategoryController::class, 'delete'])->name('deleteCategory');
    Route::post('/export-category', [CategoryController::class, 'export'])->name('exportCategory');

    //Group
    Route::get('/group',  [GroupController::class, 'index'])->name('group')->middleware('user-group');
    Route::post('/crud-group', [GroupController::class, 'crud'])->name('crudGroup');
    Route::post('/store-group', [GroupController::class, 'store'])->name('storeGroup');
    Route::post('/delete-group', [GroupController::class, 'delete'])->name('deleteGroup');
    Route::post('/export-group', [GroupController::class, 'export'])->name('exportGroup');
    Route::post('/export-people', [GroupController::class, 'exportPeople'])->name('exportPeople');
    Route::get('/table-group', [GroupController::class, 'dataTable'])->name('tableGroup');
    Route::get('/peopleform-group', [GroupController::class, 'peopleForm'])->name('peopleGroup');
    Route::post('/people-group', [GroupController::class, 'storePeople'])->name('storePeopleGroup');
    Route::get('/people-group', [GroupController::class, 'showPeople'])->name('showPeopleGroup');
    Route::get('/search-group', [GroupController::class, 'search'])->name('searchGroup');

    //Offer
    Route::match(['get', 'post'], '/offer',  [OfferController::class, 'index'])->name('offer')->middleware('user-group');
    Route::post('/crud-offer', [OfferController::class, 'crud'])->name('crudOffer');
    Route::post('/store-offer', [OfferController::class, 'store'])->name('storeOffer');
    Route::post('/delete-offer', [OfferController::class, 'delete'])->name('deleteOffer');
    Route::post('/export-offer', [OfferController::class, 'export'])->name('exportOffer');
    Route::get('/table-offer', [OfferController::class, 'dataTable'])->name('tableOffer');

    //Book
    Route::get('/book', [BookController::class, 'index'])->name('book')->middleware('user-group');
    Route::post('/crud-book', [BookController::class, 'crud'])->name('crudBook');
    Route::post('/store-book', [BookController::class, 'store'])->name('storeBook');
    Route::post('/delete-book', [BookController::class, 'delete'])->name('deleteBook');
    Route::post('/export-book', [BookController::class, 'export'])->name('exportBook');
    Route::get('/table-book', [BookController::class, 'dataTable'])->name('tableBook');

    //Book Loan
    Route::get('/book-loan', [BookLoanController::class, 'index'])->name('bookLoan');
    Route::post('/crud-book-loan', [BookLoanController::class, 'crud'])->name('crudBookLoan');
    Route::post('/store-book-loan', [BookLoanController::class, 'store'])->name('storeBookLoan');
    Route::post('/delete-book-loan', [BookLoanController::class, 'delete'])->name('deleteBookLoan');
    Route::post('/export-book-loan', [BookLoanController::class, 'export'])->name('exportBookLoan');
    Route::get('/table-book-loan', [BookLoanController::class, 'dataTable'])->name('tableBookLoan');

    //Adjective
    Route::get('/adjective',  [AdjectiveController::class, 'index'])->name('adjective')->middleware('user-group');
    Route::post('/crud-adjective', [AdjectiveController::class, 'crud'])->name('crudAdjective');
    Route::post('/store-adjective', [AdjectiveController::class, 'store'])->name('storeAdjective');
    Route::post('/delete-adjective', [AdjectiveController::class, 'delete'])->name('deleteAdjective');
    Route::post('/export-adjective', [AdjectiveController::class, 'export'])->name('exportAdjective');
    Route::get('/table-adjective', [AdjectiveController::class, 'dataTable'])->name('tableAdjective');

    //Section
    Route::get('/section',  [SectionController::class, 'index'])->name('section')->middleware('user-group');
    Route::post('/crud-section', [SectionController::class, 'crud'])->name('crudSection');
    Route::post('/store-section', [SectionController::class, 'store'])->name('storeSection');
    Route::post('/delete-section', [SectionController::class, 'delete'])->name('deleteSection');
    Route::post('/export-section', [SectionController::class, 'export'])->name('exportSection');
    Route::get('/table-section', [SectionController::class, 'dataTable'])->name('tableSection');
    Route::post('/update-default-section', [SectionController::class, 'updateDefaultSection'])->name('updateDefaultSection');

    //Station
    Route::get('/station',  [StationController::class, 'index'])->name('station')->middleware('user-group');
    Route::post('/crud-station', [StationController::class, 'crud'])->name('crudStation');
    Route::post('/store-station', [StationController::class, 'store'])->name('storeStation');
    Route::post('/delete-station', [StationController::class, 'delete'])->name('deleteStation');
    Route::post('/export-station', [StationController::class, 'export'])->name('exportStation');
    Route::get('/table-station', [StationController::class, 'dataTable'])->name('tableStation');

    //Vote
    Route::get('/vote',  [VoteController::class, 'index'])->name('vote')->middleware('user-group');
    Route::get('/table-vote',  [VoteController::class, 'dataTable'])->name('tableVote');
    Route::post('/crud-vote', [VoteController::class, 'crud'])->name('crudVote');
    Route::post('/store-vote', [VoteController::class, 'store'])->name('storeVote');
    Route::post('/delete-vote', [VoteController::class, 'delete'])->name('deleteVote');
    Route::post('/vote-report', [VoteController::class, 'report'])->name('voteReport');

    //Question
    Route::get('/question',  [QuestionController::class, 'index'])->name('question')->middleware('user-group');
    Route::post('/crud-question', [QuestionController::class, 'crud'])->name('crudQuestion');
    Route::post('/store-question', [QuestionController::class, 'store'])->name('storeQuestion');
    Route::post('/delete-question', [QuestionController::class, 'delete'])->name('deleteQuestion');
    Route::post('/result-question', [QuestionController::class, 'getResult'])->name('resultQuestion');

    //Response
    Route::get('/response', [VoteResponseController::class, 'index'])->name('response')->middleware('user-group');
    Route::post('/response', [VoteResponseController::class, 'getItems'])->name('voteResponse');

    //Counter
    Route::get('/counter',  [CounterController::class, 'index'])->name('counter')->middleware('user-group');
    Route::post('/crud-counter', [CounterController::class, 'crud'])->name('crudCounter');
    Route::post('/store-counter', [CounterController::class, 'store'])->name('storeCounter');
    Route::post('/delete-counter', [CounterController::class, 'delete'])->name('deleteCounter');
    Route::post('/export-counter', [CounterController::class, 'export'])->name('exportCounter');
    Route::match(['post', 'get'], '/counter-board',  [CounterController::class, 'boardV2'])->name('counterBoard');
    Route::post('/store-passed-time',  [CounterController::class, 'storePassedTime'])->name('storePassedTime');
    Route::post('/counter-newpresents', [CounterController::class, 'newPresents'])->name('newPresents');
    Route::post('/edit-counter', [CounterController::class, 'edit'])->name('editCounter');
    Route::post('/update-counter', [CounterController::class, 'update'])->name('updateCounter');
    Route::get('/table-counter', [CounterController::class, 'dataTable'])->name('tableCounter');
    //Package
    Route::get('/package',  [PackageController::class, 'index'])->name('package')->middleware('user-group');
    Route::post('/crud-package', [PackageController::class, 'crud'])->name('crudPackage');
    Route::post('/store-package', [PackageController::class, 'store'])->name('storePackage');
    Route::post('/delete-package', [PackageController::class, 'delete'])->name('deletePackage');
    Route::post('/export-package', [PackageController::class, 'export'])->name('exportPackage');
    Route::get('/table-package', [PackageController::class, 'dataTable'])->name('tablePackage');

    Route::get('/charge-package', [PackageController::class, 'charge'])->name('chargePackage')->middleware('user-group');
    Route::post('/charge-package', [PackageController::class, 'storeCharge'])->name('storeCharge');
    Route::post('/crud-charge', [PackageController::class, 'crudCharge'])->name('crudCharge');
    Route::post('/delete-charge', [PackageController::class, 'deleteCharge'])->name('deleteCharge');
    Route::get('/search-charge', [PackageController::class, 'searchCharge'])->name('searchCharge');
    Route::get('/table-charge', [PackageController::class, 'chargeTable'])->name('tableCharge');
    Route::post('/export-charge', [PackageController::class, 'exportCharge'])->name('exportCharge');

    // Route::get('package-report', [PackageController::class, 'reports'])->name('packageReport');

    //SMS Pattern Category
    // Route::get('/sms-pattern-category',  [SmsPatternCategoryController::class, 'index'])->name('smsPatternCategory');
    // Route::post('/crud-sms-pattern-category', [SmsPatternCategoryController::class, 'crud'])->name('crudSmsPatternCategory');
    // Route::post('/store-sms-pattern-category', [SmsPatternCategoryController::class, 'store'])->name('storeSmsPatternCategory');
    // Route::post('/delete-sms-pattern-category', [SmsPatternCategoryController::class, 'delete'])->name('deleteSmsPatternCategory');

    //SMS Pattern
    // Route::get('/sms-pattern',  [SmsPatternController::class, 'index'])->name('smsPattern');
    // Route::post('/crud-sms-pattern', [SmsPatternController::class, 'crud'])->name('crudSmsPattern');
    // Route::post('/store-sms-pattern', [SmsPatternController::class, 'store'])->name('storeSmsPattern');
    // Route::post('/delete-sms-pattern', [SmsPatternController::class, 'delete'])->name('deleteSmsPattern');
    Route::post('/status-sms-pattern', [SmsPatternController::class, 'status'])->name('statusSmsPattern');

    //Setting
    Route::get('/global', [SettingController::class, 'global'])->name('global')->middleware('user-group');
    Route::get('/sms', [SettingController::class, 'sms'])->name('sms')->middleware('user-group');
    Route::get('/personalize', [SettingController::class, 'personalize'])->name('personalize');
    Route::match(['get', 'post'], '/web-services', [SettingController::class, 'webServices'])->name('webServices');
    Route::match(['get', 'post'], '/price', [SettingController::class, 'price'])->name('price')->middleware('user-group');
    Route::match(['get', 'post'], '/print-setting', [SettingController::class, 'printSetting'])->name('printSetting');
    Route::get('/club', [SettingController::class, 'club'])->name('club')->middleware('user-group');
    Route::get('/wallet-setting', [SettingController::class, 'wallet'])->name('walletSetting');
    Route::post('/update-setting', [SettingController::class, 'update'])->name('updateSetting');

    //Price
    Route::post('/price-form', [PriceController::class, 'priceForm'])->name('priceForm');
    Route::post('/store-price', [PriceController::class, 'storePrice'])->name('storePrice');
    Route::post('/delete-price', [PriceController::class, 'deletePrice'])->name('deletePrice');

    //Club
    Route::post('/crud-club', [ClubController::class, 'crud'])->name('crudClub');
    Route::post('/store-club', [ClubController::class, 'store'])->name('storeClub');
    Route::post('/delete-club', [ClubController::class, 'delete'])->name('deleteClub');
    Route::get('/rating-club', [ClubController::class, 'showRating'])->name('ratingClub')->middleware('user-group');
    Route::get('/search-rating-club', [ClubController::class, 'searchRating'])->name('searchRatingClub');
    Route::get('/table-rating-club', [ClubController::class, 'dataTable'])->name('tableRatingClub');
    Route::post('/export-rating-club', [ClubController::class, 'export'])->name('exportRatingClub');

    //Club Logs
    Route::get('/club-log', [ClubLogController::class, 'index'])->name('clubLog')->middleware('user-group');
    Route::get('/search-club-log', [ClubLogController::class, 'search'])->name('searchClubLog');
    Route::post('/export-club-log', [ClubLogController::class, 'export'])->name('exportClubLog');
    Route::get('/table-club-log', [ClubLogController::class, 'dataTable'])->name('tableClubLog');

    //Payment Type
    Route::get('/payment-type',  [PaymentTypeController::class, 'index'])->name('paymentType')->middleware('user-group');
    Route::post('/crud-payment-type', [PaymentTypeController::class, 'crud'])->name('crudPaymentType');
    Route::post('/store-payment-type', [PaymentTypeController::class, 'store'])->name('storePaymentType');
    Route::post('/delete-payment-type', [PaymentTypeController::class, 'delete'])->name('deletePaymentType');
    Route::post('/export-payment-type', [PaymentTypeController::class, 'export'])->name('exportPaymentType');
    Route::get('/table-payment-type', [PaymentTypeController::class, 'dataTable'])->name('tablePaymentType');

    //Menu
    // Route::get('/menu',  [MenuController::class, 'index'])->name('menu');
    // Route::post('/crud-menu', [MenuController::class, 'crud'])->name('crudMenu');
    // Route::post('/store-menu', [MenuController::class, 'store'])->name('storeMenu');
    // Route::post('/delete-menu', [MenuController::class, 'delete'])->name('deleteMenu');
    Route::post('/help', [MenuController::class, 'help'])->name('helpMenu');

    //Role
    // Route::get('/role',  [RoleController::class, 'index'])->name('role');
    // Route::post('/crud-role', [RoleController::class, 'crud'])->name('crudRole');
    // Route::post('/store-role', [RoleController::class, 'store'])->name('storeRole');
    // Route::post('/delete-role', [RoleController::class, 'delete'])->name('deleteRole');

    //Payment
    Route::get('/payment',  [PaymentController::class, 'index'])->name('payment')->middleware('user-group');
    Route::post('/store-payment',  [PaymentController::class, 'store'])->name('storePayment');
    Route::post('/crud-payment',  [PaymentController::class, 'crud'])->name('crudPayment');
    Route::post('/remove-payment',  [PaymentController::class, 'remove'])->name('removePayment');
    Route::get('/search-payment',  [PaymentController::class, 'search'])->name('searchPayment');
    Route::post('/export-payment', [PaymentController::class, 'export'])->name('exportPayment');
    Route::get('/table-payment', [PaymentController::class, 'dataTable'])->name('tablePayment');
    Route::get('/sum-payment', [PaymentController::class, 'getSum'])->name('sumPayment');

    //User
    Route::get('user', [UserController::class, 'index'])->name('user')->middleware('user-group');
    Route::post('/crud-user', [UserController::class, 'crud'])->name('crudUser');
    Route::post('/store-user', [UserController::class, 'store'])->name('storeUser');
    Route::post('/delete-user', [UserController::class, 'delete'])->name('deleteUser');
    Route::post('/user-change-status', [UserController::class, 'changeStatus'])->name('user.change.status');
    Route::post('/export-user', [UserController::class, 'export'])->name('exportUser');
    Route::get('/table-user', [UserController::class, 'dataTable'])->name('tableUser');

    //User Group
    Route::get('/user-group',  [UserGroupController::class, 'index'])->name('userGroup')->middleware('user-group');
    Route::post('/crud-user-group', [UserGroupController::class, 'crud'])->name('crudUserGroup');
    Route::post('/store-user-group', [UserGroupController::class, 'store'])->name('storeUserGroup');
    Route::post('/delete-user-group', [UserGroupController::class, 'delete'])->name('deleteUserGroup');
    Route::get('/menus-user-group', [UserGroupController::class, 'showMenus'])->name('menuUserGroup');
    Route::post('/menus-user-group', [UserGroupController::class, 'storeMenus'])->name('storeMenuUserGroup');
    Route::post('/export-user-group', [UserGroupController::class, 'export'])->name('exportUserGroup');
    Route::get('/table-user-group', [UserGroupController::class, 'dataTable'])->name('tableUserGroup');

    //User Activity
    Route::get('/user-activity', [UserActivityController::class, 'index'])->name('userActivity')->middleware('user-group');
    Route::post('/crud-user-activity-manual', [UserActivityController::class, 'crud2'])->name('crud2UserActivity');
    Route::post('/store-user-activity-manual', [UserActivityController::class, 'store2'])->name('store2UserActivity');
    Route::post('/delete-user-activity', [UserActivityController::class, 'delete'])->name('deleteUserActivity');
    Route::post('/search-user-activity', [UserActivityController::class, 'search'])->name('searchUserActivity');
    Route::get('/table-user-activity', [UserActivityController::class, 'dataTable'])->name('tableUserActivity');
    Route::post('/export-user-activity', [UserActivityController::class, 'export'])->name('exportUserActivity');
    Route::get('/minute-user-activity', [UserActivityController::class, 'getMinutes'])->name('minutesUserActivity');

    //User
    Route::post('/new-password', [UserController::class, 'newPassword'])->name('newPassword');

    //User Activity
    Route::post('/crud-user-activity', [UserActivityController::class, 'crud'])->name('crudUserActivity');
    Route::post('/store-user-activity', [UserActivityController::class, 'store'])->name('storeUserActivity');

    //Faq
    Route::get('/faq', function () {
        $components = CommonQuestion::presentCoponents();
        return view('setting.faq', compact('components'));
    })->name('faq');

    //Game
    Route::post('crud-game', [GameController::class, 'crud'])->name('crudGame');
    Route::post('person-game', [GameController::class, 'searchPerson'])->name('personGame');
    Route::get('report-game', [GameController::class, 'searchReport'])->name('reportGame');
    Route::post('store-game', [GameController::class, 'store'])->name('storeGame');
    Route::post('group-store-game', [GameController::class, 'groupStore'])->name('groupStoreGame');
    Route::post('group-close-game', [GameController::class, 'groupClose'])->name('groupCloseGame');
    Route::post('close-game', [GameController::class, 'close'])->name('closeGame');
    // Route::post('close-game', [GameController::class, 'close'])->name('closeGame');
    Route::get('/game', [GameController::class, 'index'])->name('game')->middleware('user-group');
    Route::get('sum-game', [GameController::class, 'getSum'])->name('sumGame');
    Route::get('table-game', [GameController::class, 'dataTable'])->name('tableGame');
    Route::get('index-game', [GameController::class, 'indexTable'])->name('indexGame');
    Route::post('export-game', [GameController::class, 'export'])->name('exportGame');
    Route::post('/accompany-game', [GameController::class, 'showAccompany'])->name('accompanyGame');
    Route::post('edit-game', [GameController::class, 'edit'])->name('editGame');
    Route::post('update-game', [GameController::class, 'update'])->name('updateGame');
    Route::post('delete-game', [GameController::class, 'delete'])->name('deleteGame');
    Route::get('get-group-game', [GameController::class, 'getGroups'])->name('getGroupsGame');
    Route::get('price-game', [GameController::class, 'getGroupPrice'])->name('groupPriceGame');
    // Route::post('deposit-game', [GameController::class, 'updateDeposit'])->name('updateDepositGame');
    Route::get('/print/{game}', [GameController::class, 'printGame']);

    Route::post('load-game', [GameController::class, 'loadGame'])->name('loadGame');

    Route::post('crud-game-offer', [OfferController::class, 'crudGame'])->name('crudGameOffer');
    Route::post('store-game-offer', [GameController::class, 'storeOffer'])->name('storeGameOffer');

    Route::post('crud-game-personinfo', [PersonMetaController::class, 'crudGame'])->name('crudGamePersonInfo');

    Route::post('crud-game-meta', [GameMetaController::class, 'crud'])->name('crudGameMeta');
    Route::get('edit-game-meta', [GameMetaController::class, 'edit'])->name('editGameMeta');
    Route::post('crud-game-changes', [GameMetaController::class, 'crudGame'])->name('crudGameChanges');
    Route::post('store-game-changes', [GameMetaController::class, 'store'])->name('storeGameChanges');
    Route::post('update-game-changes', [GameMetaController::class, 'update'])->name('updateGameChanges');
    Route::post('delete-game-changes', [GameMetaController::class, 'delete'])->name('deleteGameChanges');

    //Factor
    Route::post('/delete-whole-factor', [FactorController::class, 'deleteFactor'])->name('deleteWholeFactor');
    Route::get('factor', [FactorController::class, 'index'])->name('factor')->middleware('user-group');
    Route::get('/search-factor', [FactorController::class, 'search'])->name('searchFactor');
    Route::post('crud-factor', [FactorController::class, 'crud'])->name('crudFactor');
    Route::post('close-factor', [FactorController::class, 'close'])->name('closeFactor');
    Route::post('offer-factor', [FactorController::class, 'offer'])->name('offerFactor');
    Route::get('/table-factor', [FactorController::class, 'dataTable'])->name('tableFactor');
    Route::get('/sum-factor', [FactorController::class, 'getSum'])->name('sumFactor');
    Route::post('/export-factor', [FactorController::class, 'export'])->name('exportFactor');
    Route::post('/product-factor', [FactorController::class, 'products'])->name('productFactor');

    //Factor Body
    Route::post('crud-factor-bodies', [FactorBodyController::class, 'crud'])->name('crudFactorBodies');
    Route::post('store-factor', [FactorBodyController::class, 'store'])->name('storeFactor');
    Route::post('update-factor', [FactorBodyController::class, 'update'])->name('updateFactor');
    Route::post('delete-factor', [FactorBodyController::class, 'delete'])->name('deleteFactor');
    Route::get('sell-product', [FactorBodyController::class, 'index'])->name('factorBody');
    Route::get('table-factor-body', [FactorBodyController::class, 'dataTable'])->name('tableFactorBody');
    Route::get('sum-factor-body', [FactorBodyController::class, 'getSum'])->name('sumFactorBody');
    Route::post('export-factor-body', [FactorBodyController::class, 'export'])->name('exportFactorBody');

    //Report
    Route::get('report', [ReportController::class, 'index'])->name('report')->middleware('user-group');
    Route::get('search-report', [ReportController::class, 'search'])->name('searchReport');
    Route::get('balance', [ReportController::class, 'balance'])->name('balance')->middleware('user-group');
    Route::get('sum-balance', [ReportController::class, 'getSum'])->name('sumBalance');

    //Chart
    Route::get('chart', [ChartController::class, 'index'])->name('chart')->middleware('user-group');
    Route::get('data-chart', [ChartController::class, 'getData'])->name('dataChart');
    Route::get('analytic-chart', [ChartController::class, 'analyticIndex'])->name('analyticChart')->middleware('user-group');
    Route::get('analytic-data', [ChartController::class, 'analyticData'])->name('dataAnalytic');

    //Cost
    Route::get('/cost',  [CostController::class, 'index'])->name('cost')->middleware('user-group');
    Route::post('/crud-cost', [CostController::class, 'crud'])->name('crudCost');
    Route::post('/store-cost', [CostController::class, 'store'])->name('storeCost');
    Route::post('/edit-cost', [CostController::class, 'edit'])->name('editCost');
    Route::post('/delete-cost', [CostController::class, 'delete'])->name('deleteCost');
    Route::get('/table-cost',  [CostController::class, 'dataTable'])->name('tableCost');
    Route::get('/sum-cost',  [CostController::class, 'getSum'])->name('sumCost');
    Route::post('/export-cost', [CostController::class, 'export'])->name('exportCost');

    //Cost Category
    Route::post('/crud-cost-category', [CostCategoryController::class, 'crud'])->name('crudCostCategory');
    Route::post('/store-cost-category', [CostCategoryController::class, 'store'])->name('storeCostCategory');
    Route::post('/delete-cost-category', [CostCategoryController::class, 'delete'])->name('deleteCostCategory');

    //Wallet
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet')->middleware('user-group');
    Route::post('crud-wallet', [WalletController::class, 'crud'])->name('crudWallet');
    Route::post('store-wallet', [WalletController::class, 'store'])->name('storeWallet');
    Route::get('search-wallet', [WalletController::class, 'search'])->name('searchWallet');
    Route::get('table-wallet', [WalletController::class, 'dataTable'])->name('tableWallet');
    Route::post('export-wallet', [WalletController::class, 'export'])->name('exportWallet');

    //Edit Report
    Route::get('{model}-report', [EditReportController::class, 'index'])->name('editReport');
    Route::get('table-editreport', [EditReportController::class, 'dataTable'])->name('tableEditReport');
    Route::post('export-editreport', [EditReportController::class, 'export'])->name('exportEditReport');

    //Request
    Route::get('request', [RequestController::class, 'index'])->name('request')->middleware('user-group');

    //Course
    Route::get('course', [CourseController::class, 'index'])->name('course')->middleware('user-group');
    Route::post('crud-course', [CourseController::class, 'crud'])->name('crudCourse');
    Route::post('store-course', [CourseController::class, 'store'])->name('storeCourse');
    Route::post('delete-course', [CourseController::class, 'delete'])->name('deleteCourse');
    Route::post('people-course', [CourseController::class, 'showPeople'])->name('peopleCourse');
    Route::post('register-course', [CourseController::class, 'showRegister'])->name('registerCourse');
    Route::post('person-course', [CourseController::class, 'syncPerson'])->name('personCourse');
    Route::post('/export-course', [CourseController::class, 'export'])->name('exportCourse');
    Route::get('/table-course', [CourseController::class, 'dataTable'])->name('tableCourse');

    //Session
    Route::get('session', [SessionController::class, 'index'])->name('session');
    Route::post('crud-session', [SessionController::class, 'crud'])->name('crudSession');
    Route::post('store-session', [SessionController::class, 'store'])->name('storeSession');
    Route::post('delete-session', [SessionController::class, 'delete'])->name('deleteSession');
    Route::post('show-session', [SessionController::class, 'show'])->name('showSession');
    Route::post('/export-session', [SessionController::class, 'export'])->name('exportSession');
    Route::get('/table-session', [SessionController::class, 'dataTable'])->name('tableSession');

    //My Payment
    Route::get('my-payment', [MyPaymentController::class, 'index'])->name('myPayment');
    Route::get('table-my-payment', [MyPaymentController::class, 'dataTable'])->name('tableMyPayment');
    Route::post('export-my-payment', [MyPaymentController::class, 'export'])->name('exportMyPayment');
    Route::get('update-my-payment', [MyPaymentController::class, 'update'])->name('updateMyPayment');

    //Sms Log
    Route::get('sms-log', [SmsLogController::class, 'index'])->name('smsLog')->middleware('user-group');
    Route::get('table-sms-log', [SmsLogController::class, 'dataTable'])->name('tableSmsLog');
    Route::post('export-sms-log', [SmsLogController::class, 'export'])->name('exportSmsLog');

    //Excel Report
    Route::get('excelreport', [ExcelReportController::class, 'index'])->name('excelReport')->middleware('user-group');
    Route::get('table-excel-report', [ExcelReportController::class, 'dataTable'])->name('tableExcelReport');
});

Route::get('/transfer', function () {
    $t = new Transfer();
    if (request()->step == 1)
        $t->truncateTables();
    if (request()->step == 2)
        $t->dry_run();
    // if (request()->step == 3) {
    //     ini_set('max_execution_time', 400);
    //     $t->paymentAll();
    // }
    if (request()->step == 4)
        $t->convertDate();
    if (request()->step == 'stg') //shamsi to geregorian
        $t->shamsitoGregorian();
})->middleware('auth');

Route::get('/testt', [TestController::class,'test']);
