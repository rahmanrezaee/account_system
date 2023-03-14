<?php

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


/*================================

    Top level of our Route Auth

===================================*/


use App\Models\Agency;


Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', function () {
        $agencies = Agency::all();
        return view('layout.home', compact('agencies'));
    });

    /* Route::get('error', function () {

         return "sorry you can not access to this page";

     });
 */


    Route::group(['middleware' => 'admin'], function () {

        //home
        Route::group(['prefix' => 'home'], function () {

            Route::get('/', 'HomeController@index')->name('home.list');
            Route::match(['get', 'post'], 'create', 'HomeController@create')->name('home.create');
            Route::match(['get', 'put'], 'update/{id}', 'CountryController@update');

        });

        //user
        Route::group(['prefix' => 'user', 'middleware' => 'admin', 'namespace' => 'Admin'], function () {
            Route::get('/getUser/{id?}', 'UsersController@index')->name('user.list');
            Route::match(['get', 'post'], 'create', 'UsersController@create')->name('user.create');
            Route::match(['get', 'put'], 'update/{id}', 'UsersController@update')->name('user.update');
            Route::match(['get', 'put'], 'doLogout', 'UsersController@doLogout')->name('user.doLogout');
            Route::delete('delete/{id}', 'UsersController@delete')->name('user.delete');

            Route::get('userInfo/{id}', 'UsersController@userInfo')->name('user.userInfo');
            Route::match(['get', 'put'], 'editUserInfo/{id}', 'UsersController@editUserInfo')->name('user.editUserInfo');
            Route::match(['get', 'put'], 'editUserSecurity/{id}', 'UsersController@editUserSecurity')->name('user.editUserSecurity');

            Route::match(['get', 'put'], 'editUserPublicInfoUpdate/{id}', 'UsersController@editUserPublicInfo')->name('user.editUserPublicInfoUpdate');
            Route::match(['get', 'put'], 'editUserSecurityInfoUpdate/{id}', 'UsersController@editUserSecurityInfo')->name('user.editUserSecurityInfoUpdate');

            Route::match(['get', 'post'], 'search/{id}', 'UsersController@search')->name('user.search');
            Route::get('changeStatus', 'UsersController@changeStatus')->name('user.changeStatus');
            Route::get('changeBranches', 'UsersController@changeBranches')->name('user.changeBranches');
        });

        //company routes
        Route::group(['prefix' => 'company', 'namespace' => 'Admin'], function () {
            Route::get('/getCompany/{id?}', 'CompanyController@index')->name('company.list');
            Route::match(['get', 'post'], 'create', 'CompanyController@create')->name('company.create');
            Route::match(['get', 'put'], 'update/{id}', 'CompanyController@update')->name('company.update');
            Route::delete('delete/{id}', 'CompanyController@delete')->name('company.delete');

            Route::match(['get', 'post'], 'search/{id}', 'CompanyController@search')->name('company.search');


        });

        // BuyFactor Routes
        Route::group(['prefix' => 'buy_factor', 'namespace' => 'Admin'], function () {

            Route::get('/', 'BuyFactorController@index')->name('buy_factor.list');
            Route::match(['get', 'post'], 'create', 'BuyFactorController@create')->name('buy_factor.create');
            Route::get('/searchByCompany', 'BuyFactorController@searchFactorByCompany')->name('searchByCompany.report');
            Route::match(['get', 'post'], 'update/{id}', 'BuyFactorController@update')->name('buy_factor.update');
            Route::delete('delete/{id}', 'BuyFactorController@delete')->name('buy_factor.delete');
            Route::get('/report', 'BuyFactorController@report')->name('buy_factor.report');
            Route::get('/report_data/{id?}', 'BuyFactorController@report_data')->name('buy_factor.report_data');
            Route::post('buyFactorPayment/', 'BuyFactorController@buyFactorPayment')->name('buy_factor_payment');
            Route::match(['get', 'put'], 'searchFactorForPay/{id}', 'BuyFactorController@searchFactorForPay')->name('search_factor_pay');
            Route::post('searchFactorForCustomerPay/{id?}', 'BuyFactorController@searchFactorForCustomerPay')->name('searchFactorForCustomerPay');
            Route::get('/detail/{id}', 'BuyFactorController@details')->name('buy_factor.detail');
            Route::post('/paymentCustomer', "CustomerController@paymentCustomer")->name("buyFactor.paymentCustomer");

        });

        // BuyProduct Routes
        Route::group(['prefix' => 'buy_product', 'namespace' => 'Admin'], function () {

            Route::get('/', 'BuyProductController@index')->name('buy_product.list');
            Route::get('/search', 'BuyProductController@searchResponse')->name('buy_product_search');
            Route::match(['get', 'post'], 'create', 'BuyProductController@create')->name('buy_product.create');
            Route::match(['get', 'post'], 'update/{id}', 'BuyProductController@update')->name('buy_product.update');
            Route::delete('delete/{id}', 'BuyProductController@delete')->name('buy_product.delete');
            Route::post('/fetch', 'BuyProductController@fetchData')->name('buy_product.fetch');
            Route::get('/report', 'BuyProductController@report')->name('buy_product.report');
            Route::get('/report_data/{id?}', 'BuyProductController@report_data')->name('buy_product.report_data');
            Route::post('/searchPaymentByCompany/{id?}', 'BuyProductController@searchFactorPaymentByCompany')->name('searchPaymentByCompany.report');

        });

        // Destroy Routes
        Route::group(['prefix' => 'destroy_product', 'namespace' => 'Admin'], function () {


            Route::get('/', 'DestroyProductController@index')->name('destroy_product.list');
            Route::get('/search', 'DestroyProductController@search')->name('destroy_product.search');
            Route::match(['get', 'post'], 'create', 'DestroyProductController@create')->name('destroy_product.create');
            Route::match(['get', 'put'], 'update/{id}', 'DestroyProductController@update')->name('destroy_product.update');
            Route::delete('/delete/{id}', 'DestroyProductController@delete')->name('destroy_product.delete');
            Route::get('/company/{id}', 'DestroyProductController@company')->name('destroy_product.company');
            Route::get('/stack/{id}', 'DestroyProductController@stack')->name('destroy_product.stack');
            Route::post('/fetch', 'DestroyProductController@fetchData')->name('destroy_product.fetch');
            Route::get('/detail/{id}', 'DestroyProductController@detail')->name('destroy_product.detail');
            Route::match(['get', 'post'], 'edit/{id}', 'DestroyProductController@edit')->name('destroy_product.edit');


        });

        // Stock Routes
        Route::group(['prefix' => 'stock', 'namespace' => 'Admin'], function () {

            Route::get('/getStore/{id?}', 'StoreController@index')->name('store.list');
            Route::get('/repprt', 'StoreController@stackReport')->name('stock.report');
            Route::post('/report_search/{id?}', 'StoreController@searchStackReport')->name('search.report');
            Route::match(['get', 'post'], 'create', 'StoreController@create')->name('store.create');
            Route::match(["get", "post"], 'update/{id}', 'StoreController@update')->name('store.update');
            Route::post('/update', 'StoreController@edit')->name('store.edit');
            Route::delete('/delete/{id}', 'StoreController@delete')->name('store.delete');
            Route::match(['get', 'post'], 'edit/{id}', 'BuyFactorController@edit')->name('buy_factor.edit');

            Route::match(['get', 'post'], 'trans', 'StoreController@transStaff')->name('store.transStaff');

            Route::match(['get', 'post'], 'search/{id}', 'StoreController@search')->name('store.search');
        });

        // Setting Routes
        Route::group(['prefix' => 'setting', 'namespace' => 'Admin'], function () {

            Route::match(['get', "post"], '/general', 'OptionsController@general')->name('options.general');
            Route::match(['get', "post"], '/personality', 'OptionsController@personality')->name('options.personality');
            Route::match(['get', "post"], '/money', 'OptionsController@money')->name('options.money');
            Route::post('/Logo', 'OptionsController@LogoUploading')->name('options.Logo');


        });

        // currency Routes
        Route::group(['prefix' => 'currency', 'namespace' => 'Admin'], function () {

            Route::get('/currency', 'CurrencyController@index')->name('currency.list');
            Route::match(['get', 'post'], 'create', 'CurrencyController@create')->name('currency.create');
            Route::match(['get', 'post'], 'update/{id}', 'CurrencyController@update')->name('currency.update');
            Route::delete('delete/{id}', 'CurrencyController@delete')->name('currency.delete');


        });

        // currency Exchanger Routes
        Route::group(['prefix' => 'currencyExchanger', 'namespace' => 'Admin'], function () {

            Route::get('/', 'currencyExchangerController@index')->name('currencyExchanger.list');
            Route::post( 'createAndUpdate', 'currencyExchangerController@createAndUpdate')->name('currencyExchanger.createAndUpdate');
            Route::delete('delete/{id}', 'currencyExchangerController@delete')->name('currencyExchanger.delete');


        });

        // reason_pay routes
        Route::group(['prefix' => 'reason_pay', 'namespace' => 'Admin'], function () {

            Route::get('/getReasonPay/{id?}', 'Reason_paysController@index')->name('reason_pay.list');
            Route::match(['get', 'post'], 'create', 'Reason_paysController@create')->name('reason_pay.create');
            Route::match(['get', 'put'], 'update/{id}', 'Reason_paysController@update')->name('reason_pay.update');
            Route::delete('delete/{id}', 'Reason_paysController@delete')->name('reason_pay.delete');
            Route::match(['get', 'post'], 'search/{id}', 'Reason_paysController@search')->name('reason_pay.search');

        });

        // expense routes
        Route::group(['prefix' => 'expense', 'namespace' => 'Admin'], function () {


            Route::get('/getExpense/{id?}', 'ExpensesController@index')->name('expense.list');
            Route::get('/report', 'ExpensesController@report')->name('expense.report');
            Route::get('/get_report', 'ExpensesController@get_report')->name('expense.get_report');
            Route::get('/report_data/{id?}', 'ExpensesController@report_data')->name('expense.report_data');
            Route::get('/pdf', 'ExpensesController@pdf')->name('expense.pdf');
            Route::match(['get', 'post'], 'create', 'ExpensesController@create')->name('expense.create');
            Route::match(['get', 'put'], 'update/{id}', 'ExpensesController@update')->name('expense.update');
            Route::delete('delete/{id}', 'ExpensesController@delete')->name('expense.delete');
            Route::match(['get', 'post'], 'search/{id}', 'ExpensesController@search')->name('expense.search');


        });

        // sale_factor routes
        Route::group(['prefix' => 'sale_factor', 'namespace' => 'Admin'], function () {

            Route::get('/getSaleFactor/{id?}', 'Sale_FactorsController@index')->name('sale_factor.list');
            Route::get('/search', 'Sale_FactorsController@searchResponse')->name('sale_factor.search');
            Route::get('/searchSale', 'Sale_FactorsController@searchResponseSele')->name('sale_factor.searchResponseSele');
            Route::get('/report', 'Sale_FactorsController@report')->name('sale_factor.report');
            Route::get('/report_data/{id?}', 'Sale_FactorsController@report_data')->name('sale_factor.report_data');
            Route::get('/pdf', 'Sale_FactorsController@pdf')->name('create_pdf');
            Route::get('/details_pdf', 'Sale_FactorsController@details_pdf')->name('sale_factor.details_pdf');
            Route::post('/fetch', 'Sale_FactorsController@fetchData')->name('sale_factor.fetch');
            Route::post('/putID', 'Sale_FactorsController@putID')->name('sale_factor.putID');
            Route::get('/get_details/{id}', 'Sale_FactorsController@get_details')->name('sale_factor.get_details');
            Route::get('/details/{id}', 'Sale_FactorsController@details')->name('sale_factor.details');
            Route::get('/print/{id}', 'Sale_FactorsController@printFactor')->name('sale_factor.print');
            Route::get('/printReturnFactor/{id}', 'Sale_FactorsController@printReturnFactor')->name('sale_factor.printReturnFactor');
            Route::match(['get', 'post'], 'create', 'Sale_FactorsController@create')->name('sale_factor.create');
            Route::match(['get', 'put'], 'update/{id}', 'Sale_FactorsController@update')->name('sale_factor.update');
            Route::match(['get', 'put'], 'returnFactor/{id}', 'Sale_FactorsController@returnFactor')->name('sale_factor.returnFactor');
            Route::delete('delete/{id}', 'Sale_FactorsController@delete')->name('sale_factor.delete');
            Route::get("/getDetail/{id}", "Sale_FactorsController@getDetailsFactore")->name("sale.getDetail");
            Route::get("/currencyExchanger", "Sale_FactorsController@currencyExchanger")->name("sale.currencyExchanger");
            Route::get("/currencyExchangerByCurrency", "Sale_FactorsController@currencyExchangerByCurrency")->name("sale.currencyExchangerByCurrency");
            Route::put("/product/update", "Sale_FactorsController@productUpdate")->name("sale.productUpdate");
            Route::match(['get', 'post'], 'search/{id}', 'Sale_FactorsController@search')->name('sale_factors.search');
            Route::match('get', 'searchStackProduct', 'Sale_FactorsController@searchStackProduct')->name('sale_factors.searchStackProduct');
            Route::match('get', 'customerPaymentList/{id}', 'Sale_FactorsController@customerPaymentList')->name('sale_factors.customerPaymentList');
            Route::get('filterFactors', 'Sale_FactorsController@filterFactors')->name('sale_factors.filterFactors');

            Route::get('getUnits', 'Sale_FactorsController@getUnits')->name('sale_factors.getUnits');
            Route::get('putMount', 'Sale_FactorsController@putMount')->name('sale_factors.putMount');
        });

        // sale_product routes
        Route::group(['prefix' => 'sale_product', 'namespace' => 'Admin'], function () {
            Route::get('/', 'Sale_ProductsController@index')->name('sale_product.list');
            Route::get('/search', 'Sale_ProductsController@searchResponse')->name('search');
            Route::get('/report', 'Sale_ProductsController@report')->name('sale_product.report');
            Route::get('/max_min', 'Sale_ProductsController@max_min')->name('sale_product.max_min');
            Route::get('/report_data', 'Sale_ProductsController@report_data')->name('sale_product.report_data');
            Route::get('/pdf', 'Sale_ProductsController@pdf')->name('sale_product.pdf');

            Route::match(['get', 'post'], 'create', 'Sale_ProductsController@create')->name('sale_product.create');
            Route::match(['get', 'put'], 'update/{id}', 'Sale_ProductsController@update')->name('sale_product.update');
            Route::delete('delete/{id}', 'Sale_ProductsController@delete')->name('sale_product.delete');
        });

        // product routes
        Route::group(['prefix' => 'product', 'namespace' => 'Admin'], function () {
            Route::get('/getProduct/{id?}', 'ProductsController@index')->name('product.list');
            Route::match(['get', 'post'], 'create', 'ProductsController@create')->name('product.create');
            Route::match(['get', 'post'], 'newProduct', 'ProductsController@newProduct')->name('product.newProduct');
            Route::match(['get', 'put'], 'update/{id}', 'ProductsController@update')->name('product.update');
            Route::delete('delete/{id}', 'ProductsController@delete')->name('product.delete');


        });

        // income routes
        Route::group(['prefix' => 'income', 'namespace' => 'Admin'], function () {
            Route::get('/', 'IncomesController@index')->name('income.list');
            Route::get('/report_data', 'IncomesController@report_data')->name('income.report_data');
            Route::get('/pdf', 'IncomesController@pdf')->name('income.pdf');

            Route::match(['get', 'post'], 'create', 'IncomesController@create')->name('income.create');
            Route::match(['get', 'put'], 'update/{id}', 'IncomesController@update')->name('income.update');
            Route::delete('delete/{id}', 'IncomesController@delete')->name('income.delete');
        });

        Route::group(['prefix' => 'backup', 'namespace' => 'Admin'], function () {

            Route::get('/', 'BackupController@index')->name('backup.index');
            Route::get('/create', 'BackupController@create')->name('backup.create');
            Route::get('/download/{file_name}', 'BackupController@download')->name('backup.download');
            Route::delete('/delete', 'BackupController@delete')->name('backup.delete');
            Route::get('/getBackup', 'BackupController@getBackup')->name('backup.getBackup');
        });

        // employee routes
        Route::group(['prefix' => 'employee', 'namespace' => 'Admin'], function () {
            Route::get('/getEmployee/{id?}', 'EmployeesController@index')->name('employee.list');
            Route::match(['get', 'post'], 'create', 'EmployeesController@create')->name('employee.create');
            Route::match(['get', 'put'], 'update/{id}', 'EmployeesController@update')->name('employee.update');
            Route::delete('delete/{id}', 'EmployeesController@delete')->name('employee.delete');

            Route::match(['get', 'post'], 'search/{id}', 'EmployeesController@search')->name('employee.search');

        });

        //employeereport
        Route::group(['prefix' => 'employeereport', 'namespace' => 'Admin'], function () {
            Route::get('/getEmployeeReport/{id?}', 'EmployeeReportsController@index')->name('employeereport.list');
            Route::get('/paymentedEmployeeReport/{id?}', 'EmployeeReportsController@paymented')->name('employeereport.paymented');
            Route::match(['get', 'post'], 'create', 'EmployeeReportsController@create')->name('employeereport.create');
            Route::match(['get', 'put'], 'update/{id}', 'EmployeeReportsController@update')->name('employeereport.update');
            Route::match(['get', 'post'], 'paymented_update/{id}', 'EmployeeReportsController@paymented_update')->name('employeereport.paymented_update');
            Route::match(['get', 'put'], 'payment/{id}', 'EmployeeReportsController@payment')->name('employeereport.payment');
            Route::delete('delete/{id}', 'EmployeeReportsController@delete')->name('employeereport.delete');
            Route::get('getSalary/{id}', 'EmployeeReportsController@getSalary')->name('employeereport.getSalary');

            Route::match(['get', 'post'], 'search/{id}', 'EmployeeReportsController@search')->name('employeereport.search');

        });

        //position
        Route::group(['prefix' => 'position', 'namespace' => 'Admin'], function () {

            Route::get('/getEmployeePosition/{id?}', 'Employee_positionsContrller@index')->name('position.list');
            Route::match(['get', 'post'], 'create', 'Employee_positionsContrller@create')->name('position.create');
            Route::match(['get', 'put'], 'update/{id}', 'Employee_positionsContrller@update')->name('position.update');
            Route::delete('delete/{id}', 'Employee_positionsContrller@delete')->name('position.delete');

            Route::match(['get', 'post'], 'search/{id}', 'Employee_positionsContrller@search')->name('position.search');

        });

        //category
        Route::group(['prefix' => 'category', 'namespace' => 'Admin'], function () {
            Route::get('/getCategory/{id?}', 'CategorysController@index')->name('category.list');

            Route::match(['get', 'post'], 'create', 'CategorysController@create')->name('category.create');
            Route::post( 'create/menal', 'CategorysController@createManval')->name('category.create.Manal');
            Route::match(['get', 'put'], 'update/{id}', 'CategorysController@update')->name('category.update');
            Route::delete('delete/{id}', 'CategorysController@delete')->name('category.delete');
            Route::match(['get', 'post'], 'search/{id}', 'CategorysController@search')->name('category.search');

            Route::get('/getCategoryOptions', 'CategorysController@getCategoryOptions')->name('category.getCategoryOptions');

        });
        //unit
        Route::group(['prefix' => 'unit', 'namespace' => 'Admin'], function () {


            Route::get('/getUnit/{id?}', 'UnitesController@index')->name('unit.list');
            Route::match(['get', 'post'], 'create', 'UnitesController@create')->name('unit.create');
            Route::post( 'create/menual', 'UnitesController@createMenual')->name('unit.create.Menual');
            Route::match(['get', 'put'], 'update/{id}', 'UnitesController@update')->name('unit.update');


            Route::delete('delete/{id}', 'UnitesController@delete')->name('unit.delete');

            Route::match(['get', 'post'], 'search/{id}', 'UnitesController@search')->name('unit.search');
            Route::get('/getUnitExchangers/', 'UnitExchangesController@index')->name('getUnitExchangers.list');
            Route::post( 'createAndUpdate', 'UnitExchangesController@createAndUpdate')->name('UnitExchange.createAndUpdate');
            Route::delete('Unitdelete/{id}', 'UnitExchangesController@delete')->name('UnitExchange.delete');


        });

        //product
        Route::group(['prefix' => 'product', 'namespace' => 'Admin'], function () {
            Route::get('/getProducts/{id?}', 'ProductController@index')->name('product.list');
            Route::match(['get', 'post'], 'create', 'ProductController@create')->name('product.create');
            Route::match(['get', 'put'], 'update/{id}', 'ProductController@update')->name('product.update');
            Route::delete('delete/{id}', 'ProductController@delete')->name('product.delete');

            Route::match(['get', 'post'], 'search/{id}', 'ProductController@search')->name('product.search');


        });

        //customer
        Route::group(['prefix' => 'customer', 'namespace' => 'Admin'], function () {
            Route::get('/getCustomer/{id?}', 'CustomerController@index')->name('customer.list');
            Route::get('/report', 'CustomerController@get_customer_report')->name('customer.report');
            Route::get('/customerReport/{id?}', 'CustomerController@customerReport')->name('customer.customerReport');
            Route::match(['get', 'post'], 'customerPayment', 'CustomerController@customerPayment')->name('customer.customer_payment');
            Route::get('/customerShowPayment', 'CustomerController@customerShowPayment')->name('customer_show_payment');
            Route::get('/customerGetPayment/{id}', 'CustomerController@customerGetPayment')->name('customer_get_payment');

            Route::match(['get', 'post'], 'search/{id}', 'CustomerController@search')->name('customer.search');

//            Route::get('/customerreport', 'CustomerController@customerreport')->name('customer.customerreport');
            Route::match(['get', 'put'], 'customerBarrow.update/{id}', 'CustomerController@updateCustomerBarrow')->name('customerBarrow.update');
            Route::match(['get', 'post'], 'create', 'CustomerController@create')->name('customer.create');
            Route::match(['get', 'put'], 'update/{id}', 'CustomerController@update')->name('customer.update');
            Route::match(['get', 'put'], 'payment/{id}', 'CustomerController@paymentUpdate')->name('customer.payment_update');
            Route::match(['get', 'put'], 'detailsPaymentUpdate/{id}', 'CustomerController@detailsPaymentUpdate')->name('customer.details_payment_update');

            Route::match(['get', 'put'], 'showDetailsPayment/{id}', 'CustomerController@showDetailsPayment')->name('show_details_payment');
            Route::delete('delete/{id}', 'CustomerController@delete')->name('customer.delete');
            Route::get("remindPayment", "CustomerController@remindPayment")->name("customer.remindPayment");
            Route::get("paymentList/{id}", "CustomerController@listRemindCustomer")->name("customer.listRemindCustomer");

        });

        //money_store
        Route::group(['prefix' => 'money_store', 'namespace' => 'Admin'], function () {
            Route::get('/getMoneyStore/{id?}', 'MoneyStoresController@index')->name('money_store.list');
            Route::match(['get', 'post'], 'create', 'MoneyStoresController@create')->name('money_store.create');
            Route::match(['get', 'put'], 'update/{id}', 'MoneyStoresController@update')->name('money_store.update');
            Route::match(['get', 'put'], 'report', 'MoneyStoresController@report')->name('money_store.report');
            Route::post('searchMoneyStoreReport', 'MoneyStoresController@searchMoneyStoreReport')->name('money_store.search_report');
            Route::delete('delete/{id}', 'MoneyStoresController@delete')->name('money_store.delete');
            Route::delete('paymentChangeStatus/{id}', 'MoneyStoresController@paymentChangeStatus')->name('money_store.payment_status');
            Route::match(['get', 'post'], 'search/{id}', 'MoneyStoresController@search')->name('money_store.search');

            Route::match(['get', 'post'],'/resumecha/{id?}', 'MoneyStoresController@resumecha')->name('money_store.resumecha');

        });

    });


// owner routes
    Route::group(['prefix' => 'owner', 'namespace' => 'Admin'], function () {
        Route::get('/getOwner/{id?}', 'OwnerController@index')->name('owner.list');
        Route::match(['get', 'post'], 'create', 'OwnerController@create')->name('owner.create');
        Route::match(['get', 'put'], 'update/{owner_id}', 'OwnerController@update')->name('owner.update');
        Route::delete('delete/{id}', 'OwnerController@delete')->name('owner.delete');

        Route::match(['get', 'post'], 'search/{id}', 'OwnerController@search')->name('owner.search');


    });


//    Route Money Transfer
    Route::group(['prefix' => 'money_exchange', 'namespace' => 'Admin'], function () {
        Route::get('/getTransferMoney/{id?}', 'TransferMoneyController@index')->name('money_transfer.list');
        Route::match(['get', 'post'], 'create', 'TransferMoneyController@create')->name('money_transfer.create');
        Route::match(['get', 'post'], 'update/{id}', 'TransferMoneyController@update')->name('money_transfer.update');
        Route::delete('delete/{id}', 'TransferMoneyController@delete')->name('money_transfer.delete');

        Route::get('/report', 'TransferMoneyController@report')->name('money_transfer.report');
        Route::get('/get_report', 'TransferMoneyController@get_report')->name('money_transfer.get_report');
        Route::get('/report_data/{id?}', 'TransferMoneyController@report_data')->name('money_transfer.report_data');

        Route::match(['get', 'post'], 'search/{id}', 'TransferMoneyController@search')->name('money_transfer.search');

    });


//    Route Transfer Product
    Route::group(['prefix' => 'transfer_product', 'namespace' => 'Admin'], function () {
        Route::get('create', 'TransferProductController@create')->name('product_transfer.create');
        Route::get('/', 'TransferProductController@index@index')->name('product_transfer.list');

//        Route::match(['get', 'post'], 'create', 'OwnerController@create')->name('owner.create');
//        Route::match(['get', 'put'], 'update/{owner_id}', 'OwnerController@update')->name('owner.update');
//        Route::delete('delete/{id}', 'OwnerController@delete')->name('owner.delete');

    });


//    Route Car
    Route::group(['prefix' => 'car', 'namespace' => 'Admin'], function () {
        Route::get('/', 'CarController@index')->name('car.list');
        Route::match(['get', 'post'], 'create', 'CarController@create')->name('car.create');
        Route::match(['get', 'post'], 'update/{id}', 'CarController@update')->name('car.update');
        Route::delete('delete/{id}', 'CarController@delete')->name('car.delete');

        Route::match(['get', 'post'], 'search/{id}', 'CarController@search')->name('car.search');


    });

    //    Route Catch Money
    Route::group(['prefix' => 'catch_money', 'namespace' => 'Admin'], function () {

        Route::get('/', 'CatchMoneyController@index')->name('catch_money.list');
        Route::match(['get', 'post'], 'create', 'CatchMoneyController@create')->name('catch_money.create');
        Route::match(['get', 'post'], 'update/{id}', 'CatchMoneyController@update')->name('catch_money.update');
        Route::delete('delete/{id}', 'CatchMoneyController@delete')->name('catch_money.delete');

        Route::get('/report', 'CatchMoneyController@report')->name('catch_money.report');
        Route::get('/get_report', 'CatchMoneyController@get_report')->name('catch_money.get_report');
        Route::get('/report_data', 'CatchMoneyController@report_data')->name('catch_money.report_data');

        Route::match(['get', 'post'], 'search/{id}/{status_id}/{number_of_pagination?}', 'CatchMoneyController@search')->name('catch_money.search');

    });


//    Add Money To Money_Store
    Route::group(['prefix' => 'add_money', 'namespace' => 'Admin'], function () {
        Route::get('/', 'AddMoneyController@index')->name('add_money.list');
        Route::match(['get', 'post'], 'create', 'AddMoneyController@create')->name('add_money.create');
        Route::match(['get', 'post'], 'update/{id}', 'AddMoneyController@update')->name('add_money.update');
        Route::delete('delete/{id}', 'AddMoneyController@delete')->name('add_money.delete');
        Route::get('/report', 'AddMoneyController@report')->name('add_money.report');
        Route::get('/get_report', 'AddMoneyController@get_report')->name('add_money.get_report');
        Route::get('/report_data', 'AddMoneyController@report_data')->name('add_money.report_data');

        Route::match(['get', 'post'], 'search/{id}', 'AddMoneyController@search')->name('add_money.search');


    });

    //    Route Transfer Product
    Route::group(['prefix' => 'transfer_product', 'namespace' => 'Admin'], function () {
        Route::get('/getTransferProduct/{id?}', 'TransferProductController@index')->name('transfer_product.list');
        Route::match(['get', 'post'], 'create', 'TransferProductController@create')->name('transfer_product.create');
        Route::match(['get', 'post'], 'update/{id}', 'TransferProductController@update')->name('transfer_product.update');
        Route::delete('delete/{id}', 'TransferProductController@delete')->name('transfer_product.delete');

    });

// Money Exchange
    Route::group(['prefix' => 'money', 'namespace' => 'Admin'], function () {
        Route::match(['get', 'post'], 'create', 'StoreMoneyController@create')->name('money_exchange.create');
        Route::get('/getMoneyExchange/{id?}', 'StoreMoneyController@index')->name('money_exchange.list');

        Route::match(['get', 'post'], 'update/{id}', 'StoreMoneyController@update')->name('money_exchange.update');
        Route::delete('delete/{id}', 'StoreMoneyController@delete')->name('money_exchange.delete');

    });

//    Route Money Transfer
    Route::group(['prefix' => 'money_transfer', 'namespace' => 'Admin'], function () {
        Route::get('/getTransferMoney/{id?}', 'TransferMoneyController@index')->name('money_transfer.list');
        Route::match(['get', 'post'], 'create', 'TransferMoneyController@create')->name('money_transfer.create');
        Route::match(['get', 'post'], 'update/{id}', 'TransferMoneyController@update')->name('money_transfer.update');
        Route::delete('delete/{id}', 'TransferMoneyController@delete')->name('money_transfer.delete');

        Route::match(['get', 'post'], 'search/{id}', 'TransferMoneyController@search')->name('money_transfer.search');

    });


//    Route Car
    Route::group(['prefix' => 'car', 'namespace' => 'Admin'], function () {
        Route::get('/getCarIncome/{id?}', 'CarController@index')->name('car.list');
        Route::match(['get', 'post'], 'create', 'CarController@create')->name('car.create');
        Route::match(['get', 'post'], 'update/{id}', 'CarController@update')->name('car.update');
        Route::delete('delete/{id}', 'CarController@delete')->name('car.delete');

    });

    //    Route Catch Money
//    Route::group(['prefix' => 'catch_money', 'namespace' => 'Admin'], function () {
//        Route::get('/getCatchMoney/{id?}', 'CatchMoneyController@index')->name('catch_money.list');
//        Route::match(['get', 'post'], 'create', 'CatchMoneyController@create')->name('catch_money.create');
//        Route::match(['get', 'post'], 'update/{id}', 'CatchMoneyController@update')->name('catch_money.update');
//        Route::delete('delete/{id}', 'CatchMoneyController@delete')->name('catch_money.delete');
//
//    });

//    Route Revenue
    Route::group(['prefix' => 'revenue', 'namespace' => 'Admin'], function () {
        Route::get('/', 'RevenueController@index')->name('revenue.list');
        Route::match(['get', 'post'], 'create', 'RevenueController@create')->name('revenue.create');
        Route::match(['get', 'post'], 'update/{id}', 'RevenueController@update')->name('revenue.update');
        Route::delete('delete/{id}', 'RevenueController@delete')->name('revenue.delete');

    });

    //    Route First Equipment Money
    Route::group(['prefix' => 'first_equipment_money', 'namespace' => 'Admin'], function () {
        Route::get('/getFirstEquipment/{id?}', 'FirstEquipmentMoneyController@index')->name('first_equipment_money.list');
        Route::match(['get', 'post'], 'create', 'FirstEquipmentMoneyController@create')->name('first_equipment_money.create');
        Route::match(['get', 'post'], 'update/{id}', 'FirstEquipmentMoneyController@update')->name('first_equipment_money.update');
        Route::delete('delete/{id}', 'FirstEquipmentMoneyController@delete')->name('first_equipment_money.delete');

        Route::match(['get', 'post'], 'search/{id}', 'FirstEquipmentMoneyController@search')->name('first_equipment_money.search');

    });

    //    Agency Routes
    Route::group(['prefix' => 'agency', 'namespace' => 'Admin'], function () {
        Route::get('/getAgency/{id?}', 'AgencyController@index')->name('agency.list');
        Route::match(['get', 'post'], 'create', 'AgencyController@create')->name('agency.create');
        Route::match(['get', 'post'], 'update/{id}', 'AgencyController@update')->name('agency.update');
        Route::delete('delete/{id}', 'AgencyController@delete')->name('agency.delete');

        Route::match(['get', 'post'], 'search/{id}', 'AgencyController@search')->name('agency.search');
    });
});
















