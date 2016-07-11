$(function() {

    /*
     * 日期选择
     */
    $('#id-date-range-picker').daterangepicker({
        format: 'YYYY-MM-DD',
        separator: ' 至 '
    }).prev().on(ace.click_event, function() {
        $(this).next().focus();
    });

    /*
     * 全选
     */
    $('table th input:checkbox').on('click', function() {
        var that = this;
        $(this).closest('table').find('tr > td:first-child input:checkbox, .choose-rec').each(function() {
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });
    });

    /*
     * 选中单条
     */
    $('td:first-child input:checkbox').on('click', function() {
        var that = this;
        $(this).closest('tr').find('.choose-rec').each(function() {
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });

    });

    /*
     * 表单提交
     */
    $("#order-list").Validform({
        tiptype: 4,
        btnSubmit: ".btn-purple",
        showAllError: true,
        beforeSubmit: function(curform) {
            var date = $.trim($('#id-date-range-picker').val());
            if (date.length > 0) {
                var start = $.trim($('input[name="daterangepicker_start"]').val());
                var end = $.trim($('input[name="daterangepicker_end"]').val());
                $('#date_start').val(start);
                $('#date_end').val(end);
            }
        }
    });

    //override dialog's title function to allow for HTML titles
    $.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
        _title: function(title) {
            var $title = this.options.title || '&nbsp;'
            if (("title_html" in this.options) && this.options.title_html == true)
                title.html($title);
            else
                title.text($title);
        }
    }));

    /*
     * 导出excel
     */
    $('.excel').on('click', function(e) {
        var id = $(this).attr('id');
        e.preventDefault();
        var order = [];
        $('input[name="choose-box"]:checked').each(function() {
            order.push($(this).attr('order-id'));
        })
        var search = window.location.search;
        if (!search) {
            var url = '/buy/orderList?type=' + id;
        } else {
            var url = '/buy/orderList' + search + '&type=' + id;
        }
        if (order.length > 0) {
            var orderStr = order.join(",");
            window.location.href = url + '&excel=' + orderStr;
        } else {
            var totalNumber = $('#total-number').html();
            if (!totalNumber) {
                totalNumber = 0;
            }
            var content = '你尚未选择某个订单，将导出当前<strong>' + totalNumber + '</strong>个订单详情！';
            $('#dialog-message .alert-info').html(content);
            // Dialogs
            $("#dialog-message").removeClass('hide').dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i>你确定吗？</h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='icon-trash bigger-110'></i>&nbsp; Yes",
                        "class": "btn btn-danger btn-xs",
                        click: function() {
                            window.location.href = url + '&excel=all';
                            $(this).dialog("close");
                        }
                    }
                    ,
                    {
                        html: "<i class='icon-remove bigger-110'></i>&nbsp; No",
                        "class": "btn btn-xs",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }
                ]
            });
        }
    })

    /*
     * 按照供应商采购
     */
    $('#buy-from-supplier').on('click', function(e) {
        e.preventDefault();
        var order = [];
        $('input[name="rec-choose-box"]:checked').each(function() {
            order.push($(this).attr('rec-id'));
        })
        if (order.length > 0) {
            var orderStr = order.join(",");
            window.location.href = '/buy/buyBySupplier/id/' + orderStr;
        } else {
            var totalNumber = $('#total-number').html();
            var content = '你尚未选择某个订单！';
            $('#dialog-message .alert-info').html(content);
            // Dialogs
            $("#dialog-message").removeClass('hide').dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i>请选择订单</h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='icon-trash bigger-110'></i>&nbsp; Yes",
                        "class": "btn btn-danger btn-xs",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }
                ]
            });
        }
    })

    /*
     * 按照订单采购
     */
    $('#buy-from-order').on('click', function(e) {
        e.preventDefault();
        var order = [];
        $('input[name="rec-choose-box"]:checked').each(function() {
            order.push($(this).attr('rec-id'));
        })
        if (order.length > 0) {
            var orderStr = order.join(",");
            window.location.href = '/buy/buyByOrder/id/' + orderStr;
        } else {
            var totalNumber = $('#total-number').html();
            var content = '你尚未选择某个订单！';
            $('#dialog-message .alert-info').html(content);
            // Dialogs
            $("#dialog-message").removeClass('hide').dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i>请选择订单</h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='icon-trash bigger-110'></i>&nbsp; Yes",
                        "class": "btn btn-danger btn-xs",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }
                ]
            });
        }
    })
})

function changeInput(obj, id, cat) {
    var content = $.trim($(obj).html());
    if (content.indexOf("input") == -1 && content.indexOf("textarea") == -1) {
        var html = '<input type="text" style="width: 50px;margin: 0;" name="' + id + '_' + cat + '" value="' + content + '" onblur="saveChangeInput(this,' + "'" + id + "'" + ',' + "'" + cat + "'" + ')">';
        $(obj).html(html);
        $(obj).find('input').focus();
    }
}

function saveChangeInput(obj, id, cat) {
    var price = $.trim($(obj).val());
    if (price.length == 0) {
        price = '0';
    }
    $.ajax({
        url: "/buy/updateOrderFee",
        type: "post",
        data: 'order_id=' + id + '&cat=' + cat + '&price=' + price,
        dataType: 'json',
        success: function(data) {
            if (data.status == true) {
                $('#' + id + '_' + cat + '_fee').html(price);
            } else {
                alert(data.message);
                return false;
            }
        }
    });
}