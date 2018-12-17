<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            @can('admin')
                <li class="treeview active">
                    <a href="#">
                        <i class="fa fa-dashboard "></i> <span>系统管理</span>
                        <span class="pull-right-container"></span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/admin/permissions"><i class="fa fa-circle-o"></i> 权限管理</a></li>
                        <li><a href="/admin/users"><i class="fa fa-circle-o"></i> 用户管理</a></li>
                        <li><a href="/admin/roles"><i class="fa fa-circle-o"></i> 角色管理</a></li>
                    </ul>
                </li>
            @endcan
            @can('website')
                <li class="active treeview">
                    <a href="/admin/websites">
                        <i class="fa fa-dashboard"></i> <span>网站管理</span>
                    </a>
                </li>
            @endcan
            @can('product')
                <li class="active treeview">
                    <a href="/admin/products">
                        <i class="fa fa-dashboard"></i> <span>商品管理</span>
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
            @can('category')
                <li class="active treeview">
                    <a href="/admin/category/list">
                        <i class="fa fa-dashboard"></i> <span>分类管理</span>
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
            @can('comment')
                <li class="active treeview">
                    <a href="/admin/comment/list/{{2}}">
                        <i class="fa fa-dashboard"></i> <span>评论管理</span>
                    </a>
                </li>
            @endcan
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
