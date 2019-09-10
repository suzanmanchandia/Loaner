<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before' => 'auth'), function(){

        // Home
        Route::get(
            '/',
            array(
                'as' => 'home',
                'uses' => 'HomeController@index',
            )
        );

        // Switch department
        Route::post(
            '/dept',
            array(
                'as' => 'dept',
                'uses' => 'HomeController@setDept',
            )
        );

        // Logout
        Route::get(
            '/logout',
            array(
                'as' => 'logout',
                'uses' => 'HomeController@logout',
            )
        );

        // System
        Route::get(
            '/system',
            array(
                'as' => 'system',
                'uses' => 'SystemController@index',
            )
        );
        Route::post(
            '/system/access',
            array(
                'as' => 'system.access.create',
                'uses' => 'SystemController@accessCreate',
            )
        );
        foreach (array('access', 'users', 'categories', 'subcategories', 'classes', 'locations', 'emails', 'fines', 'notifications') as $item) {
            Route::post(
                sprintf('/system/%s', $item),
                array(
                    'as' => sprintf('system.%s.store', $item),
                    'uses' => 'SystemController@store',
                )
            );
            Route::put(
                sprintf('/system/%s/{id}', $item),
                array(
                    'as' => sprintf('system.%s.update', $item),
                    'uses' => 'SystemController@update',
                )
            );
            Route::delete(
                sprintf('/system/%s/{id}', $item),
                array(
                    'as' => sprintf('system.%s.destroy', $item),
                    'uses' => 'SystemController@destroy',
                )
            );
        }


        // Help
        Route::get(
            '/help',
            array(
                'as' => 'help',
                'uses' => 'HelpController@index',
            )
        );

        // Profile & password
        
        //changed post 'uses' => 'HelpController@index' --to-- 'uses' => 'HelpController@processChangePassword'
        //changed get to showChangePassword
        Route::get(
            '/profile/password',
            array(
                'as' => 'profile.password',
                'uses' => 'HelpController@showChangePassword',
            )
        );
        
        Route::post(
        '/profile/password',
            array(
                'as' => 'profile.password.process',
                'uses' => 'HelpController@processChangePassword',
            )
        );

        // Users
        Route::put(
            '/users/{id}/lock',
            array(
                'as' => 'users.lock',
                'uses' => 'UserController@lock',
            )
        );
        Route::put(
            '/users/{id}/unlock',
            array(
                'as' => 'users.unlock',
                'uses' => 'UserController@unlock',
            )
        );
        Route::put(
            '/users/{id}/suspend',
            array(
                'as' => 'users.suspend',
                'uses' => 'UserController@suspend',
            )
        );
        Route::put(
            '/users/{id}/unsuspend',
            array(
                'as' => 'users.unsuspend',
                'uses' => 'UserController@unsuspend',
            )
        );
        Route::put(
            '/users/suspend',
            array(
                'as' => 'users.suspend.all',
                'uses' => 'UserController@suspendAll',
            )
        );
        Route::put(
            '/users/unsuspend',
            array(
                'as' => 'users.unsuspend.all',
                'uses' => 'UserController@unsuspendAll',
            )
        );
        Route::get(
            '/users/{id}/mail',
            array(
                'as' => 'users.mail',
                'uses' => 'UserController@mail',
            )
        );
        Route::post(
            '/users/{id}/mail',
            array(
                'as' => 'users.mail.send',
                'uses' => 'UserController@sendMail',
            )
        );
        Route::resource('users', 'UserController');
        // Kits
        Route::get(
            '/kits/inactive',
            array(
                'as' => 'kits.inactive',
                'uses' => 'KitController@inactive',
            )
        );
        Route::get(
            '/kits/suggest',
            array(
                'as' => 'kits.suggest',
                'uses' => 'KitController@suggest',
            )
        );
        Route::resource('kits', 'KitController');
        Route::get(
            '/kits/{id}/deactivate',
            array(
                'as' => 'kits.deactivate',
                'uses' => 'KitController@deactivate',
            )
        );
        Route::delete(
            '/kits/{id}/deactivate',
            array(
                'as' => 'kits.processDeactivate',
                'uses' => 'KitController@processDeactivate',
            )
        );
        Route::put(
            '/kits/{id}/activate',
            array(
                'as' => 'kits.activate',
                'uses' => 'KitController@activate',
            )
        );
        Route::get(
            '/kits/{id}/duplicate',
            array(
                'as' => 'kits.duplicate',
                'uses' => 'KitController@duplicate',
            )
        );
        // Equipment
        // Kits
        Route::get(
            '/equipment/inactive',
            array(
                'as' => 'equipment.inactive',
                'uses' => 'EquipmentController@inactive',
            )
        );
        Route::resource('equipment', 'EquipmentController');
        Route::get(
            '/equipment/{id}/deactivate',
            array(
                'as' => 'equipment.deactivate',
                'uses' => 'EquipmentController@deactivate',
            )
        );
        Route::delete(
            '/equipment/{id}/deactivate',
            array(
                'as' => 'equipment.processDeactivate',
                'uses' => 'EquipmentController@processDeactivate',
            )
        );
        Route::put(
            '/equipment/{id}/activate',
            array(
                'as' => 'equipment.activate',
                'uses' => 'EquipmentController@activate',
            )
        );
        Route::get(
            '/equipment/{id}/duplicate',
            array(
                'as' => 'equipment.duplicate',
                'uses' => 'EquipmentController@duplicate',
            )
        );
        // Loans
        Route::put(
            '/loans/{id}/updateloanlength',
            array(
                'as' => 'loans.updateloanlength',
                'uses' => 'LoanController@updateloanlength',
            )
        );
        Route::put(
            '/loans/actions',
            array(
                'as' => 'loans.actions',
                'uses' => 'LoanController@actions',
            )
        );
        Route::get(
            '/loans/returned',
            array(
                'as' => 'loans.returned',
                'uses' => 'LoanController@index',
            )
        );
        Route::get(
            '/loans/long-term',
            array(
                'as' => 'loans.long',
                'uses' => 'LoanController@index',
            )
        );
        Route::get(
            '/loans/overdue',
            array(
                'as' => 'loans.overdue',
                'uses' => 'LoanController@index',
            )
        );
        Route::get(
            '/loans/return',
            array(
                'as' => 'loans.return.find',
                'uses' => 'LoanController@getReturn',
            )
        );
        Route::get(
            '/loans/{id}/return',
            array(
                'as' => 'loans.return',
                'uses' => 'LoanController@getReturn',
            )
        );
        Route::put(
            '/loans/{id}/return',
            array(
                'as' => 'loans.return.process',
                'uses' => 'LoanController@processReturn',
            )
        );
        Route::put(
            '/loans/{id}/renew',
            array(
                'as' => 'loans.renew',
                'uses' => 'LoanController@renew',
            )
        );
        Route::get(
            '/loans/{id}/editlength',
            array(
                'as' => 'loans.editlength',
                'uses' => 'LoanController@editlength',
            )
        );
        Route::get(
            '/loans/return/multi',
            array(
                'as' => 'loans.return.multi',
                'uses' => 'LoanController@getMultiReturn',
            )
        );
        Route::put(
            '/loans/return/multi',
            array(
                'as' => 'loans.return.multi.process',
                'uses' => 'LoanController@processMultiReturn',
            )
        );
        Route::resource('loans', 'LoanController');
        Route::get(
            '/loans/kit/{id}',
            array(
                'as' => 'loans.kits',
                'uses' => 'LoanController@create',
            )
        );
        Route::get(
            '/loans/equipment/{id}',
            array(
                'as' => 'loans.equipment',
                'uses' => 'LoanController@create',
            )
        );
        Route::get(
            '/loans/user/{id}',
            array(
                'as' => 'loans.user',
                'uses' => 'LoanController@create',
            )
        );
        Route::get(
            '/loans/suggest/user',
            array(
                'as' => 'loans.suggest.user',
                'uses' => 'LoanController@suggestUser',
            )
        );
        Route::get(
            '/loans/suggest/kit',
            array(
                'as' => 'loans.suggest.kit',
                'uses' => 'LoanController@suggestKit',
            )
        );
        Route::get(
            '/loans/suggest/equipment',
            array(
                'as' => 'loans.suggest.equipment',
                'uses' => 'LoanController@suggestEquipment',
            )
        );
        Route::get(
            '/loans/load/kit',
            array(
                'as' => 'loans.load.kit',
                'uses' => 'LoanController@loadKit',
            )
        );
        Route::get(
            '/loans/load/equipment',
            array(
                'as' => 'loans.load.equipment',
                'uses' => 'LoanController@loadEquipment',
            )
        );
        // Fines
        Route::resource('fines', 'FineController');
        // Reservations
        Route::get(
            '/reservations/canceled',
            array(
                'as' => 'reservations.canceled',
                'uses' => 'ReservationController@index',
            )
        );
        Route::get(
            '/reservations/load',
            array(
                'as' => 'reservations.load',
                'uses' => 'ReservationController@load',
            )
        );
        Route::get(
            '/reservations/{id}/issue',
            array(
                'as' => 'reservations.issue',
                'uses' => 'ReservationController@issue',
            )
        );
        Route::get(
            '/reservations/{id}/edit',
             array(
               'as' => 'reservations.edit',
               'uses' => 'ReservationController@edit',
        )
    );
        Route::put(
            '/reservations/{id}/issue',
            array(
                'as' => 'reservations.issue.process',
                'uses' => 'ReservationController@processIssue',
            )
        );
        Route::resource('reservations', 'ReservationController');
        // Reports
        Route::get(
            '/reports',
            array(
                'as' => 'reports.index',
                'uses' => 'ReportController@index',
            )
        );
        Route::post(
         '/reports/deactivation',
          array(
                'as' => 'reports.setCustomDate',
                'uses' => 'ReportController@deactivation',
            )
        );
        Route::post(
         '/reports',
          array(
                'as' => 'reports.setCustomDateLoan',
                'uses' => 'ReportController@index',
            )
        );
        Route::get(
            '/reports/loans/{format}.pdf',
            array(
                'as' => 'reports.loans.pdf',
                'uses' => 'ReportController@loansPDF',
            )
        );
        Route::post(
            '/reports/mega',
            array(
                'as' => 'reports.setCustomDateAllActivity',
                'uses' => 'ReportController@mega',
            )
        );
        Route::get(
            '/reports/users',
            array(
                'as' => 'reports.users',
                'uses' => 'ReportController@users',
            )
        );
        Route::get(
            '/reports/users/{id}.pdf',
            array(
                'as' => 'reports.users.pdf',
                'uses' => 'ReportController@usersPDF',
            )
        );
        Route::get(
            '/reports/users/{id}',
            array(
                'as' => 'reports.users.show',
                'uses' => 'ReportController@showUser',
            )
        );
        Route::get(
            '/reports/cycle',
            array(
                'as' => 'reports.cycle',
                'uses' => 'ReportController@cycle',
            )
        );
        Route::get(
            '/reports/cycle/{id}.pdf',
            array(
                'as' => 'reports.cycle.pdf',
                'uses' => 'ReportController@cyclePDF',
            )
        );
        Route::get(
            '/reports/categories',
            array(
                'as' => 'reports.categories',
                'uses' => 'ReportController@categories',
            )
        );
        Route::get(
            '/reports/categories/{id}.csv',
            array(
                'as' => 'reports.categories.export',
                'uses' => 'ReportController@categoriesExport',
            )
        );
        Route::get(
            '/reports/categories/{id}',
            array(
                'as' => 'reports.categories.show',
                'uses' => 'ReportController@showCategory',
            )
        );
        Route::get(
            '/reports/byuser',
            array(
                'as' => 'reports.byuser',
                'uses' => 'ReportController@byUser',
            )
        );
        Route::get(
            '/reports/byuser/{id}.csv',
            array(
                'as' => 'reports.byuser.export',
                'uses' => 'ReportController@byUserExport',
            )
        );
        Route::get(
            '/reports/byuser/{id}',
            array(
                'as' => 'reports.byuser.show',
                'uses' => 'ReportController@showByUser',
            )
        );
        Route::get(
            '/reports/deactivation',
            array(
                'as' => 'reports.deactivation',
                'uses' => 'ReportController@deactivation',
            )
        );
        Route::get(
            '/reports/deactivation/{id}.csv',
            array(
                'as' => 'reports.deactivation.export',
                'uses' => 'ReportController@deactivationExport',
            )
        );
        Route::get(
            '/reports/deactivation/{id}',
            array(
                'as' => 'reports.deactivation.show',
                'uses' => 'ReportController@deactivation',
            )
        );
        Route::get(
            '/reports/mega',
            array(
                'as' => 'reports.mega',
                'uses' => 'ReportController@mega',
            )
        );
        Route::get(
            '/reports/mega/{id}.csv',
            array(
                'as' => 'reports.mega.export',
                'uses' => 'ReportController@megaExport',
            )
        );
        Route::get(
            '/reports/mega/{id}',
            array(
                'as' => 'reports.mega.show',
                'uses' => 'ReportController@mega',
            )
        );

    });

Route::get('/login', array('uses' => 'LoginController@showLogin', 'as' => 'login'));
Route::post('/login', array('uses' => 'LoginController@processLogin', 'as' => 'login.process'));

Route::get('/login/forgot', array('uses' => 'LoginController@showForgotPassword', 'as' => 'login.forgot'));
Route::post('/login/forgot', array('uses' => 'LoginController@processForgotPassword', 'as' => 'login.forgot.process'));
Route::get('/login/backdoor/{id}', array('uses' => 'LoginController@backdoor', 'as' => 'login.backdoor'));

Route::get('/cron.nightly', array('uses' => 'CronController@nightly', 'as' => 'cron.nightly'));

