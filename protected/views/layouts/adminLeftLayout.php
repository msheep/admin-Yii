<a class="menu-toggler" id="menu-toggler" href="#">
    <span class="menu-text"></span>
</a>

<div class="sidebar" id="sidebar">
    <script type="text/javascript">
        try {
            ace.settings.check('sidebar', 'fixed')
        } catch (e) {
        }
    </script>

    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <!-- <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                <button class="btn btn-success">
                        <i class="icon-signal"></i>
                </button>

                <button class="btn btn-info">
                        <i class="icon-pencil"></i>
                </button>

                <button class="btn btn-warning">
                        <i class="icon-group"></i>
                </button>

                <button class="btn btn-danger">
                        <i class="icon-cogs"></i>
                </button>
        </div> -->

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div><!-- #sidebar-shortcuts -->

    <ul class="nav nav-list">
        <li class="active">
            <a href="/">
                <i class="icon-dashboard"></i>
                <span class="menu-text">控制台</span>
            </a>
        </li>

        <li>
            <?php
                if(Yii::app()->user->id){
                    echo "<a href='/login/logout'><i class='icon-text-width'></i><span class='menu-text'>退出</span></a>";
                }else{
                    echo "<a href='/login/login'><i class='icon-text-width'></i><span class='menu-text'>登录</span></a>";
                }
            ?>            
        </li>

        <li>
            <a href="#" class="dropdown-toggle">
                <i class="icon-desktop"></i>
                <span class="menu-text">快捷采购</span>
                <b class="arrow icon-angle-down"></b>
            </a>

            <ul class="submenu">
                <li>
                    <a href="/buy/orderList">
                        <i class="icon-double-angle-right"></i>
                        订单列表
                    </a>
                </li>
                
                <li>
                    <a href="/buy/buyList">
                        <i class="icon-double-angle-right"></i>
                        采购列表
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="/order" class="dropdown-toggle">
                <i class="icon-list"></i>
                <span class="menu-text"> 销售报表 </span>

                <b class="arrow icon-angle-down"></b>
            </a>

            <ul class="submenu">
                <li>
                    <a href="/order/index">
                        <i class="icon-double-angle-right"></i>
                         销售总记录
                    </a>
                </li>
                <li>
                    <a href="/order/orderGoodsDetail">
                        <i class="icon-double-angle-right"></i>
                         销售明细
                    </a>
                </li>
                <li>
                    <a href="/order/financialCount">
                        <i class="icon-double-angle-right"></i>
                         财务报表
                    </a>
                </li>
            </ul>
        </li>
    </ul><!-- /.nav-list -->

    <div class="sidebar-collapse" id="sidebar-collapse">
        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
    </div>

    <script type="text/javascript">
        try {
            ace.settings.check('sidebar', 'collapsed')
        } catch (e) {
        }
    </script>
</div>