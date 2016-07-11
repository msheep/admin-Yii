<div class="breadcrumbs" id="breadcrumbs">
    <script type="text/javascript">
        try {
            ace.settings.check('breadcrumbs', 'fixed')
        } catch (e) {
        }
    </script>

    <?php
    $this->widget('zii.widgets.CBreadcrumbs', array(
        'links' => $this->breadcrumbs,
        'htmlOptions' => array('class' => 'breadcrumb', 'style' => 'line-height:40px'),
        'tagName' => 'ul',
        'activeLinkTemplate' => '<li><a href="{url}">{label}</a></li>',
        'inactiveLinkTemplate' => '<li class="active">{label}</li>',
        'homeLinkTemplate' => '<li><i class="icon-home home-icon"></i><a href="{url}">{label}</a></li>',
        'separator' => '',
    ));
    ?>
    <!-- .breadcrumb -->
    <div class="nav-search" id="nav-search">
        <form class="form-search">
            <span class="input-icon">
                <input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
                <i class="icon-search nav-search-icon"></i>
            </span>
        </form>
    </div><!-- #nav-search -->
</div>

<div class="page-content">
    <!-- PAGE CONTENT BEGINS -->
    <?php echo $content; ?>
    <!-- PAGE CONTENT ENDS -->
</div><!-- /.page-content -->