<?php
Route::group(['prefix' => 'admin'], function() {

    Route::get('/', '\App\Admin\Controllers\LoginController@index');
    Route::get('/test', '\App\Admin\Controllers\HomeController@test');
    Route::get('/login', '\App\Admin\Controllers\LoginController@index');
    Route::post('/login', '\App\Admin\Controllers\LoginController@login');
    Route::get('/logout', '\App\Admin\Controllers\LoginController@logout');
    Route::get('/test/test', '\App\Admin\Controllers\TestController@index');

    // 需要登陆的
    Route::group(['middleware' => 'auth:admin'], function(){
        Route::get('/home', '\App\Admin\Controllers\HomeController@index');

        //TODO ======本地上传图片======
        //Route::post('/common/up', '\App\Admin\Controllers\CommonController@up');
        Route::post('/commons/img/upload', '\App\Admin\Controllers\CommonController@imageUpload');

        //TODO ======线上上传图片======
//        Route::post('/commons/imgUpload', '\App\Admin\Controllers\CommonController@imgUpload');
        //获取子分类
        Route::post('/common/getCategoryInfo', '\App\Admin\Controllers\CommonController@getCategoryInfo');
        // 系统管理
        Route::group(['middleware' => 'can:system'], function(){
            // 用户管理
            Route::get('/users', '\App\Admin\Controllers\UserController@index');
            Route::get('/users/create', '\App\Admin\Controllers\UserController@create');
            Route::post('/users/store', '\App\Admin\Controllers\UserController@store');
            Route::get('/users/{user}/role', '\App\Admin\Controllers\UserController@role');
            Route::post('/users/{user}/role', '\App\Admin\Controllers\UserController@storeRole');

            // 角色管理
            Route::get('/roles', '\App\Admin\Controllers\RoleController@index');
            Route::get('/roles/create', '\App\Admin\Controllers\RoleController@create');
            Route::post('/roles/store', '\App\Admin\Controllers\RoleController@store');
            Route::get('/roles/{role}/permission', '\App\Admin\Controllers\RoleController@permission');
            Route::post('/roles/{role}/permission', '\App\Admin\Controllers\RoleController@storePermission');

            // 权限管理
            Route::get('/permissions', '\App\Admin\Controllers\PermissionController@index');
            Route::get('/permissions/create', '\App\Admin\Controllers\PermissionController@create');
            Route::post('/permissions/store', '\App\Admin\Controllers\PermissionController@store');
        });

        // 网站模块
        Route::group(['middleware' => 'can:website'], function() {
            Route::get('/websites/update/{website}', '\App\Admin\Controllers\WebsiteController@update');
            Route::post('/websites/{website}/updatedo', '\App\Admin\Controllers\WebsiteController@updateDo');
            Route::post('/websites/remark', '\App\Admin\Controllers\WebsiteController@updateRemark');
            // 网站模块
            Route::resource('websites', '\App\Admin\Controllers\WebsiteController', ['only' => [
                'index', 'create', 'store'
            ]]);
        });
        // 店铺模块
        Route::group(['middleware' => 'can:website'], function() {
            Route::get('/shop/update/{shop}', '\App\Admin\Controllers\ShopController@update');
            Route::post('/shop/updatedo', '\App\Admin\Controllers\ShopController@updateDo');
            // 网站模块
            Route::resource('shop', '\App\Admin\Controllers\ShopController', ['only' => [
                'index', 'create', 'store'
            ]]);
        });
        // 商品模块
        Route::group(['middleware' => 'can:product'], function() {
            Route::get('/products', '\App\Admin\Controllers\ProductController@index');
            Route::get('/products/create', '\App\Admin\Controllers\ProductController@create');
            Route::post('/products/import', '\App\Admin\Controllers\ProductController@import');
            Route::post('/products/selectId', '\App\Admin\Controllers\ProductController@selectId');
            Route::post('/products/selectIdUp', '\App\Admin\Controllers\ProductController@selectIdUp');
            Route::post('/products/add', '\App\Admin\Controllers\ProductController@add');
            Route::any('/products/match/{product}/{attribute_name_id}/{attribute_value_id}', '\App\Admin\Controllers\ProductController@match');
            Route::any('/products/match/{product}', '\App\Admin\Controllers\ProductController@match');
            Route::Post('/products/match', '\App\Admin\Controllers\ProductController@saveWords');
            Route::get('/products/update/{product}', '\App\Admin\Controllers\ProductController@update');
            Route::post('/products/{product}/updatedo', '\App\Admin\Controllers\ProductController@updateDo');
            Route::post('/products/{product}/updateinfo', '\App\Admin\Controllers\ProductController@updateInfo');
            Route::any('/products/{product}/updateprice', '\App\Admin\Controllers\ProductController@updatePrice');
            Route::any('/goods/add', '\App\Admin\Controllers\GoodController@goodAdd');
            Route::post('/goods/pass', '\App\Admin\Controllers\GoodController@goodPass');
            Route::any('/goods/goodUpdate/{id}', '\App\Admin\Controllers\GoodController@goodUpdate');
            Route::Post('/products/keyWordSearch', '\App\Admin\Controllers\ProductController@keyWordSearch');
            Route::get('/products/sphinx', '\App\Admin\Controllers\ProductController@sphinx');
            Route::any('/products/goodsMatch', '\App\Admin\Controllers\ProductController@goodsMatch');
            Route::any('/products/goodsMatchDo', '\App\Admin\Controllers\ProductController@goodsMatchDo');
            Route::any('/products/goodsMatchSave', '\App\Admin\Controllers\ProductController@goodsMatchSave');
            Route::post('/products/productsMatchDo', '\App\Admin\Controllers\ProductController@productsMatchDo');
            Route::post('/products/productsMatchSave', '\App\Admin\Controllers\ProductController@productsMatchSave');
            Route::post('/products/{product}/updatefinish', '\App\Admin\Controllers\ProductController@updateFinish');
            Route::post('/products/goodsMatchSave', '\App\Admin\Controllers\ProductController@goodsMatchSave');
            Route::post('/products/batchSetCate', '\App\Admin\Controllers\ProductController@batchSetCate');
            Route::post('/products/batchSetCateByProductId', '\App\Admin\Controllers\ProductController@batchSetCateByProductId');
            Route::post('/products/batchSetKeywordByProductId', '\App\Admin\Controllers\ProductController@batchSetKeywordByProductId');
            Route::post('/products/batchSetSpecsByProductId', '\App\Admin\Controllers\ProductController@batchSetSpecsByProductId');
            Route::post('/products/delGoodsMatch', '\App\Admin\Controllers\ProductController@delGoodsMatch');
        });

        // 品牌模块
        Route::group(['middleware' => 'can:brand'], function(){
            Route::get('/brand', '\App\Admin\Controllers\BrandController@index');
            Route::post('/brand/create', '\App\Admin\Controllers\BrandController@create');
            Route::get('/brand/list/', '\App\Admin\Controllers\BrandController@brandList');
            Route::get('/brand/update/{brand}', '\App\Admin\Controllers\BrandController@brandUpdate');
            Route::post('/brand/save', '\App\Admin\Controllers\BrandController@brandSave');
            Route::post('/brand/remark', '\App\Admin\Controllers\BrandController@updateRemark');

        });

        // 分类模块
        Route::group(['middleware' => 'can:category'], function(){
            Route::post('/category/getHierarchyCategory', '\App\Admin\Controllers\CategoryController@getHierarchyCategory');
            Route::get('/category/list', '\App\Admin\Controllers\CategoryController@categoryList');
            Route::get('/category/update/{category}', '\App\Admin\Controllers\CategoryController@categoryUpdate');
            Route::post('/category/save/{category}', '\App\Admin\Controllers\CategoryController@categorySave');
            Route::get('/category/matching', '\App\Admin\Controllers\CategoryController@categoryMatching');
            Route::post('/category/matchingDo', '\App\Admin\Controllers\CategoryController@categoryMatchingDo');
            Route::post('/category/getLevelCategory', '\App\Admin\Controllers\CategoryController@getLevelCategory');
            Route::resource('category', '\App\Admin\Controllers\CategoryController', [
                'only' => ['index', 'create'],
            ]);
            Route::get('/category/tree', '\App\Admin\Controllers\CategoryController@categoryTree');
            Route::post('/category/setDisplay', '\App\Admin\Controllers\CategoryController@setDisplay');
        });
        // 攻略模块
        Route::group(['middleware' => 'can:strategy'], function () {
            Route::get('/strategy', '\App\Admin\Controllers\StrategyController@index');
            Route::get('/strategy/import', '\App\Admin\Controllers\StrategyController@strategyImport');
            Route::post('/strategy/getImport', '\App\Admin\Controllers\StrategyController@getImportInfo');
            Route::post('/strategy/strategyImportAdd', '\App\Admin\Controllers\StrategyController@strategyImportAdd');
            Route::post('/strategy/create', '\App\Admin\Controllers\StrategyController@create');
            Route::get('/strategy/list', '\App\Admin\Controllers\StrategyController@strategyList');
            Route::get('/strategy/update/{strategy}', '\App\Admin\Controllers\StrategyController@strategyUpdate');
            Route::post('/strategy/save/{strategy}', '\App\Admin\Controllers\StrategyController@strategySave');
            Route::get('/strategy/detail/{strategy}', '\App\Admin\Controllers\StrategyController@strategyDetail');
            Route::post('/strategy/upload', '\App\Admin\Controllers\StrategyController@sliderUpload');
        });
        //前台用户模块
        Route::group(['middleware' => 'can:user'], function () {
            Route::get('/frontDeskUser/list', '\App\Admin\Controllers\FrontDeskUserController@frontDeskUserList');
            Route::get('/frontDeskUser/update/{frontDeskUser}', '\App\Admin\Controllers\FrontDeskUserController@frontDeskUserUpdate');
            Route::post('/frontDeskUser/save', '\App\Admin\Controllers\FrontDeskUserController@frontDeskUserSave');
        });
        //前台评论模块
        Route::group(['middleware' => 'can:comment'], function () {
            Route::get('/comment/list/{commentStatus}', '\App\Admin\Controllers\CommentController@commentList');
//            Route::get('/comment/list1', '\App\Admin\Controllers\CommentController@commentNewList');
            Route::post('/comment/update', '\App\Admin\Controllers\CommentController@commentSave');
        });
        //攻略作者模块
        Route::get('/author/list', '\App\Admin\Controllers\AuthorController@authorList');
        Route::get('/author', '\App\Admin\Controllers\AuthorController@index');
        Route::post('/author/create', '\App\Admin\Controllers\AuthorController@create');
        Route::get('/author/update/{author}', '\App\Admin\Controllers\AuthorController@authorUpdate');
        Route::post('/author/save/{author}', '\App\Admin\Controllers\AuthorController@authorSave');
    });
});
