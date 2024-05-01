<?php

use App\Http\Livewire\Aboutus;
use App\Http\Livewire\Accordion;
use App\Http\Livewire\Alerts;
use App\Http\Livewire\Avatar;
use App\Http\Livewire\Background;
use App\Http\Livewire\Badge;
use App\Http\Livewire\Blog;
use App\Http\Livewire\BlogDetails;
use App\Http\Livewire\Border;
use App\Http\Livewire\Breadcrumbs;
use App\Http\Livewire\Buttons;
use App\Http\Livewire\Calendar;
use App\Http\Livewire\Cards;
use App\Http\Livewire\Carousel;
use App\Http\Livewire\ChartChartjs;
use App\Http\Livewire\ChartEchart;
use App\Http\Livewire\ChartFlot;
use App\Http\Livewire\ChartMorris;
use App\Http\Livewire\ChartPeity;
use App\Http\Livewire\ChartSparkline;
use App\Http\Livewire\Chat;
use App\Http\Livewire\CheckOut;
use App\Http\Livewire\Collapse;
use App\Http\Livewire\Contacts;
use App\Http\Livewire\Counters;
use App\Http\Livewire\Display;
use App\Http\Livewire\Draggablecards;
use App\Http\Livewire\Dropdown;
use App\Http\Livewire\EditPost;
use App\Http\Livewire\Emptypage;
use App\Http\Livewire\Error404;
use App\Http\Livewire\Error500;
use App\Http\Livewire\Error501;
use App\Http\Livewire\Extras;
use App\Http\Livewire\Faq;
use App\Http\Livewire\FileAttachedTags;
use App\Http\Livewire\FileDetails;
use App\Http\Livewire\FileManager;
use App\Http\Livewire\FileManager1;
use App\Http\Livewire\Flex;
use App\Http\Livewire\Forgot;
use App\Http\Livewire\FormAdvanced;
use App\Http\Livewire\FormEditor;
use App\Http\Livewire\FormElements;
use App\Http\Livewire\FormLayouts;
use App\Http\Livewire\FormSizes;
use App\Http\Livewire\FormValidation;
use App\Http\Livewire\FormWizards;
use App\Http\Livewire\Gallery;
use App\Http\Livewire\Height;
use App\Http\Livewire\Icons;
use App\Http\Livewire\Icons10;
use App\Http\Livewire\Icons11;
use App\Http\Livewire\Icons12;
use App\Http\Livewire\Icons2;
use App\Http\Livewire\Icons3;
use App\Http\Livewire\Icons4;
use App\Http\Livewire\Icons5;
use App\Http\Livewire\Icons6;
use App\Http\Livewire\Icons7;
use App\Http\Livewire\Icons8;
use App\Http\Livewire\Icons9;
use App\Http\Livewire\ImageCompare;
use App\Http\Livewire\Images;
use App\Http\Livewire\Index;
use App\Http\Livewire\Index1;
use App\Http\Livewire\Index2;
use App\Http\Livewire\Invoice;
use App\Http\Livewire\ListGroup;
use App\Http\Livewire\Lockscreen;
use App\Http\Livewire\Mail;
use App\Http\Livewire\MailCompose;
use App\Http\Livewire\MailRead;
use App\Http\Livewire\MailSettings;
use App\Http\Livewire\MapLeaflet;
use App\Http\Livewire\MapVector;
use App\Http\Livewire\Margin;
use App\Http\Livewire\MediaObject;
use App\Http\Livewire\Modals;
use App\Http\Livewire\Navigation;
use App\Http\Livewire\Notification;
use App\Http\Livewire\Padding;
use App\Http\Livewire\Pagination;
use App\Http\Livewire\Popover;
use App\Http\Livewire\Position;
use App\Http\Livewire\Pricing;
use App\Http\Livewire\ProductCart;
use App\Http\Livewire\ProductDetails;
use App\Http\Livewire\Profile;
use App\Http\Livewire\ProfileNotifications;
use App\Http\Livewire\Progress;
use App\Http\Livewire\Rangeslider;
use App\Http\Livewire\Rating;
use App\Http\Livewire\Reset;
use App\Http\Livewire\Search;
use App\Http\Livewire\Settings;
use App\Http\Livewire\Shop;
use App\Http\Livewire\Signin;
use App\Http\Livewire\Signup;
use App\Http\Livewire\Spinners;
use App\Http\Livewire\SweetAlert;
use App\Http\Livewire\Switcherpage;
use App\Http\Livewire\TableBasic;
use App\Http\Livewire\TableData;
use App\Http\Livewire\Tabs;
use App\Http\Livewire\Tags;
use App\Http\Livewire\Thumbnails;
use App\Http\Livewire\Timeline;
use App\Http\Livewire\Toast;
use App\Http\Livewire\Todotask;
use App\Http\Livewire\Tooltip;
use App\Http\Livewire\Treeview;
use App\Http\Livewire\Typography;
use App\Http\Livewire\Underconstruction;
use App\Http\Livewire\Userlist;
use App\Http\Livewire\WidgetNotification;
use App\Http\Livewire\Widgets;
use App\Http\Livewire\Width;
use App\Http\Livewire\WishList;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\sebayatregisterController;
use App\Http\Controllers\User\userController;
use App\Http\Controllers\Superadmin\SuperAdminController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Pandit\PanditController;






/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

## user login
Route::controller(userController::class)->group(function() {
    Route::get('/register', 'userregister')->name('user-register');
    Route::post('store', 'store')->name('store');

    Route::get('/', 'userlogin')->name('userlogin');
    Route::get('/demo', 'demo')->name('demo');

    Route::post('user/authenticate', 'userauthenticate')->name('userauthenticate');
    Route::post('user/logout', 'userlogout')->name('userlogout');

});

## admin login
Route::controller(AdminController::class)->group(function() {
        
    Route::get('/admin', 'adminlogin')->name('adminlogin');
    Route::post('admin/authenticate', 'authenticate')->name('adminauthenticate');
    Route::post('admin/logout', 'adminlogout')->name('adminlogout');

});

## super admin login
Route::controller(SuperAdminController::class)->group(function() {
        
    Route::get('superadmin/', 'superadminlogin')->name('login');
    Route::post('superadmin/authenticate', 'authenticate')->name('authenticate');
    Route::get('superadmin/dashboard', 'dashboard')->name('dashboard');
    Route::post('superadmin/logout', 'sulogout')->name('sulogout');
});


##super admin routes
Route::prefix('superadmin')->middleware(['superadmin'])->group(function () {
    Route::controller(SuperAdminController::class)->group(function() {
        
        Route::get('superadmin/dashboard', 'dashboard')->name('dashboard');
    });
    Route::get('/addadmin', [SuperAdminController::class, 'addadmin']);
    Route::post('/saveadmin', [SuperAdminController::class, 'saveadmin']);
    Route::get('/editadmin/{id}', [SuperAdminController::class, 'editadmin'])->name('editadmin');
    Route::post('/update/{id}', [SuperAdminController::class, 'update'])->name('update');
    Route::get('/dltadmin/{id}', [SuperAdminController::class, 'dltadmin'])->name('dltadmin');

    Route::get('/adminlist', [SuperAdminController::class, 'adminlist']);
});

## admin routes
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::controller(AdminController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
    
    });
    
            Route::get('/sebayatregister', [sebayatregisterController::class, 'sebayatregister']);
            Route::post('/saveregister', [sebayatregisterController::class, 'saveregister'])->name('saveregister');
            Route::put('/reject/{id}', [sebayatregisterController::class, 'reject'])->name('reject');
            Route::put('/approve/{id}', [sebayatregisterController::class, 'approve'])->name('approve');
            Route::get('/sebayatlist', [sebayatregisterController::class, 'sebayatlist'])->name('sebayatlist');
            Route::get('/editsebayat/{userid}', [sebayatregisterController::class, 'editsebayat'])->name('editsebayat');
    
            Route::get('/viewsebayat/{userid}', [sebayatregisterController::class, 'viewsebayat'])->name('viewsebayat');
            Route::get('/dltsebayat/{userid}', [sebayatregisterController::class, 'dltsebayat'])->name('dltsebayat');
    
            // Route::put('/updateuserinfo/{id}', [sebayatregisterController::class, 'updateuserinfo']);
            Route::put('/childupdate/{id}', [sebayatregisterController::class, 'childupdate']);
    
            Route::put('/updateuserinfo/{userid}', [sebayatregisterController::class, 'updateuserinfo'])->name('updateUserInfo');
            Route::put('/updatefamilyinfo/{userid}', [sebayatregisterController::class, 'updateFamilyInfo'])->name('updateFamilyInfo');
            Route::post('/updatechildInfo', [sebayatregisterController::class, 'updateChildInfo'])->name('updateChildInfo');
           
            Route::get('/updatechildstatus/{userid}', [sebayatregisterController::class, 'updatechildstatus'])->name('updatechildstatus');

            Route::put('/updateidinfo/{userid}', [sebayatregisterController::class, 'updateIdInfo'])->name('updateIdInfo');

            Route::get('/updateIdstatus/{userid}', [sebayatregisterController::class, 'updateIdstatus'])->name('updateIdstatus');
    
            Route::put('/updateAddressInfo/{userid}', [sebayatregisterController::class, 'updateAddressInfo'])->name('updateAddressInfo');
            Route::put('/updateBankInfo/{userid}', [sebayatregisterController::class, 'updateBankInfo'])->name('updateBankInfo');

            Route::put('/updatenewAddress', [sebayatregisterController::class, 'updatenewAddress'])->name('updatenewAddress');
            Route::put('/updatenewBankInfo', [sebayatregisterController::class, 'updatenewBankInfo'])->name('updatenewBankInfo');

            Route::put('/updateotherInfo/{userid}', [sebayatregisterController::class, 'updateotherInfo'])->name('updateOtherInfo');


});



// user routes
Route::prefix('user')->middleware(['user'])->group(function () {

  
   
    Route::controller(userController::class)->group(function() {
        Route::get('/dashboard', 'dashboard')->name('user.dashboard');
        Route::get('/sebayatregister', 'sebayatregister')->name('user.sebayatregister');
        Route::get('/sebayatprofile', 'sebayatprofile')->name('user.sebayatprofile');

        Route::get('/download-user-image', 'downloadUserImage')->name('download.user.image');


        
    });
    Route::put('/updateuserinfo/{userid}', [userController::class, 'updateuserinfo'])->name('updateUserInfo');
    Route::put('/updatefamilyinfo/{userid}', [userController::class, 'updateFamilyInfo'])->name('updateFamilyInfo');
    Route::post('/updatechildInfo', [userController::class, 'updateChildInfo'])->name('updateChildInfo');
           
    Route::get('/updatechildstatus/{userid}', [userController::class, 'updatechildstatus'])->name('updatechildstatus');

    Route::put('/updateidinfo/{userid}', [userController::class, 'updateIdInfo'])->name('updateIdInfo');

    Route::get('/updateIdstatus/{userid}', [userController::class, 'updateIdstatus'])->name('updateIdstatus');
    
    Route::put('/updateAddressInfo/{userid}', [userController::class, 'updateAddressInfo'])->name('updateAddressInfo');
    Route::put('/updateBankInfo/{userid}', [userController::class, 'updateBankInfo'])->name('updateBankInfo');

    Route::put('/updatenewAddress', [userController::class, 'updatenewAddress'])->name('updatenewAddress');
    Route::put('/updatenewBankInfo', [userController::class, 'updatenewBankInfo'])->name('updatenewBankInfo');

    Route::put('/updateotherInfo/{userid}', [userController::class, 'updateotherInfo'])->name('updateOtherInfo');

});


/// pandit routes

Route::controller(PanditController::class)->group(function() {
   

    Route::get('/pandit', 'panditlogin')->name('pandit.panditlogin');
  

});


Route::get('aboutus', Aboutus::class);
Route::get('accordion', Accordion::class);
Route::get('alerts', Alerts::class);
Route::get('avatar', Avatar::class);
Route::get('background', Background::class);
Route::get('badge', Badge::class);
Route::get('blog-details', BlogDetails::class);
Route::get('blog', Blog::class);
Route::get('border', Border::class);
Route::get('breadcrumbs', Breadcrumbs::class);
Route::get('buttons', Buttons::class);
Route::get('calendar', Calendar::class);
Route::get('cards', Cards::class);
Route::get('carousel', Carousel::class);
Route::get('chart-chartjs', ChartChartjs::class);
Route::get('chart-echart', ChartEchart::class);
Route::get('chart-flot', ChartFlot::class);
Route::get('chart-morris', ChartMorris::class);
Route::get('chart-peity', ChartPeity::class);
Route::get('chart-sparkline', ChartSparkline::class);
Route::get('chat', Chat::class);
Route::get('check-out', CheckOut::class);
Route::get('collapse', Collapse::class);
Route::get('contacts', Contacts::class);
Route::get('counters', Counters::class);
Route::get('draggablecards', Draggablecards::class);
Route::get('display', Display::class);
Route::get('dropdown', Dropdown::class);
Route::get('edit-post', EditPost::class);
Route::get('emptypage', Emptypage::class);
Route::get('error404', Error404::class);
Route::get('error500', Error500::class);
Route::get('error501', Error501::class);
Route::get('extras', Extras::class);
Route::get('faq', Faq::class);
Route::get('file-attached-tags', FileAttachedTags::class);
Route::get('file-details', FileDetails::class);
Route::get('file-manager', FileManager::class);
Route::get('file-manager1', FileManager1::class);
Route::get('flex', Flex::class);
Route::get('forgot', Forgot::class);
Route::get('form-advanced', FormAdvanced::class);
Route::get('form-editor', FormEditor::class);
Route::get('form-elements', FormElements::class);
Route::get('form-layouts', FormLayouts::class);
Route::get('form-sizes', FormSizes::class);
Route::get('form-validation', FormValidation::class);
Route::get('form-wizards', FormWizards::class);
Route::get('gallery', Gallery::class);
Route::get('height', Height::class);
Route::get('icons', Icons::class);
Route::get('icons2', Icons2::class);
Route::get('icons3', Icons3::class);
Route::get('icons4', Icons4::class);
Route::get('icons5', Icons5::class);
Route::get('icons6', Icons6::class);
Route::get('icons7', Icons7::class);
Route::get('icons8', Icons8::class);
Route::get('icons9', Icons9::class);
Route::get('icons10', Icons10::class);
Route::get('icons11', Icons11::class);
Route::get('icons12', Icons12::class);
Route::get('image-compare', ImageCompare::class);
Route::get('images', Images::class);
// Route::get('index', Index::class);
Route::get('index1', Index1::class);
Route::get('index2', Index2::class);
Route::get('invoice', Invoice::class);
Route::get('list-group', ListGroup::class);
Route::get('lockscreen', Lockscreen::class);
Route::get('mail-compose', MailCompose::class);
Route::get('mail-read', MailRead::class);
Route::get('mail-settings', MailSettings::class);
Route::get('mail', Mail::class);
Route::get('map-leaflet', MapLeaflet::class);
Route::get('map-vector', MapVector::class);
Route::get('margin', Margin::class);
Route::get('media-object', MediaObject::class);
Route::get('modals', Modals::class);
Route::get('navigation', Navigation::class);
Route::get('notification', Notification::class);
Route::get('padding', Padding::class);
Route::get('pagination', Pagination::class);
Route::get('popover', Popover::class);
Route::get('position', Position::class);
Route::get('pricing', Pricing::class);
Route::get('product-cart', ProductCart::class);
Route::get('product-details', ProductDetails::class);
Route::get('profile-notifications', ProfileNotifications::class);
Route::get('profile', Profile::class);
Route::get('progress', Progress::class);
Route::get('rangeslider', Rangeslider::class);
Route::get('rating', Rating::class);
Route::get('reset', Reset::class);
Route::get('search', Search::class);
Route::get('settings', Settings::class);
Route::get('shop', Shop::class);
// Route::get('signin', Signin::class);
// Route::get('signup', Signup::class);
Route::get('spinners', Spinners::class);
Route::get('sweet-alert', SweetAlert::class);
Route::get('switcherpage', Switcherpage::class);
Route::get('table-basic', TableBasic::class);
Route::get('table-data', TableData::class);
Route::get('tabs', Tabs::class);
Route::get('tags', Tags::class);
Route::get('thumbnails', Thumbnails::class);
Route::get('timeline', Timeline::class);
Route::get('toast', Toast::class);
Route::get('todotask', Todotask::class);
Route::get('tooltip', Tooltip::class);
Route::get('treeview', Treeview::class);
Route::get('typography', Typography::class);
Route::get('underconstruction', Underconstruction::class);
Route::get('userlist', Userlist::class);
Route::get('widget-notification', WidgetNotification::class);
Route::get('widgets', Widgets::class);
Route::get('width', Width::class);
Route::get('wish-list', WishList::class);
