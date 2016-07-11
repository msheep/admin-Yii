<?php  
    $this->widget('zii.widgets.CListView', array(
        'id'=>'gbox_grid-table',
        'dataProvider'=>$dataProvider,
        'itemView'=>$itemView,
        'template'=>'<div class="table-responsive"><div id="sample-table-2_wrapper" class="dataTables_wrapper" role="grid">{items}<div class="row"><div class="col-sm-6">{summary}</div><div class="col-sm-6">{pager}</div></div></div></div>',
        'summaryText'=>'<div class="total">共 <strong id="total-number">{count}</strong> 条记录</div>',
    	'emptyText' => '',
        'itemsTagName'=>'table',
    	'itemsCssClass'=>'table table-striped table-bordered table-hover dataTable ui-jqgrid-btable ',
    	'pagerCssClass'=>'dataTables_paginate paging_bootstrap',
    	'ajaxUpdate'=>FALSE,
    	'cssFile'=>FALSE,
        'pager'=>array(
            'htmlOptions'=>array('class'=>'pagination'),
            'class'=> 'CLinkPager',
            'cssFile'=>FALSE,
            'header'=> '',
            'firstPageLabel' => '首页',
            'prevPageLabel' => '上一页',
            'nextPageLabel' => '下一页',
            'lastPageLabel' => '末页',
            'maxButtonCount'=> '10'
         )
    ));
?>   