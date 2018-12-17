<header class="main-header">
    <!-- Logo -->
    {{--<a href="/adminlte/index2.html" class="logo">--}}
        {{--<!-- mini logo for sidebar mini 50x50 pixels -->--}}
        {{--<span class="logo-mini"></span>--}}
        {{--<!-- logo for regular state and mobile devices -->--}}
        {{--<span class="logo-lg">全球最低价后台管理</span>--}}
    {{--</a>--}}
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" style="margin-left: 0px;">
        <!-- Sidebar toggle button-->
        {{--<a href="/adminlte/#" class="sidebar-toggle" data-toggle="offcanvas" role="button">--}}
            {{--<span class="sr-only">Toggle navigation</span>--}}
        {{--</a>--}}
        <div class="navbar-custom-menu" style="float: left;">
            <ul class="nav navbar-nav">
                @can('product')
                    <li class="active treeview">
                        <a href="/admin/products">
                            <i class="fa fa-dashboard"></i> <span>商品管理</span>
                        </a>
                    </li>
                @endcan
                @can('product')
                    <li class="active treeview">
                        <a href="/admin/products/goodsMatch">
                            <i class="fa fa-dashboard"></i> <span>匹配管理</span>
                        </a>
                    </li>
                @endcan
                @can('brand')
                    <li class="active treeview">
                        <a href="/admin/brand/list">
                            <i class="fa fa-android"></i> <span>品牌管理</span>
                        </a>
                    </li>
                @endcan
                @can('strategy')
                    <li class="active treeview">
                        <a href="/admin/strategy/list">
                            <i class="fa fa-dashboard"></i> <span>攻略管理</span>
                        </a>
                    </li>
                @endcan
                @can('user')
                    <li class="active treeview">
                        <a href="/admin/frontDeskUser/list">
                            <i class="fa fa-dashboard"></i> <span>用户管理</span>
                        </a>
                    </li>
                @endcan
                {{--@can('comment')--}}
                    {{--<li class="active treeview">--}}
                        {{--<a href="/admin/comment/list/{{2}}">--}}
                            {{--<i class="fa fa-dashboard"></i> <span>评论管理</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                {{--@endcan--}}
                @can('website')
                    <li class="active treeview">
                        <a href="/admin/websites">
                            <i class="fa fa-dashboard"></i> <span>网站管理</span>
                        </a>
                    </li>
                @endcan
                @can('website')
                    <li class="active treeview">
                        <a href="/admin/shop">
                            <i class="fa fa-dashboard"></i> <span>店铺管理</span>
                        </a>
                    </li>
                @endcan
                @can('category')
                    <li class="active treeview">
                        <a href="/admin/category/list">
                            <i class="fa fa-dashboard"></i> <span>分类管理</span>
                        </a>
                    </li>
                @endcan
                {{--@can('author')--}}
                    {{--<li class="active treeview">--}}
                        {{--<a href="/admin/author/list">--}}
                            {{--<i class="fa fa-dashboard"></i> <span>攻略作者管理</span>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                {{--@endcan--}}
                @can('system')
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-dashboard "></i> <span>系统管理</span>
                            <span class="pull-right-container"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="/admin/permissions"><i class="fa fa-circle-o"></i> 权限管理</a>
                                    <a href="/admin/users"><i class="fa fa-circle-o"></i> 用户管理</a>
                                    <a href="/admin/roles"><i class="fa fa-circle-o"></i> 角色管理</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                @endcan
            </ul>

        </div>
        <ul class="nav navbar-nav" style="float: right">
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="/adminlte/#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="/adminlte/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                    <span class="hidden-xs">{{\Auth::guard("admin")->user()->name}}</span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="/admin/logout" class="btn btn-default btn-flat">登出</a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>