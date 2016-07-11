jQuery(function($) {
    $('.dialogs,.comments').slimScroll({
        height: '300px'
    });


    $('#datetimepicker1').datetimepicker({
        language: 'en',
        pickDate: true,
        pickTime: true,
        inputMask: true
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


    /*点击按钮改变订单shipping status*/
    $('.action').on('click', function(e) {
        var recId = $.trim($(this).parent().siblings('input[name="rec-id"]').val());
        var actionValue = $(this).attr('value');
        var orderId = $.trim($('input[name="order-id"]').val());
        // Dialogs
        $("#dialog-message").removeClass('hide').dialog({
            resizable: false,
            modal: true,
            title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i>备注信息</h4></div>",
            title_html: true,
            buttons: [
                {
                    html: "<i class='icon-trash bigger-110'></i>&nbsp; Yes",
                    "class": "btn btn-danger btn-xs",
                    click: function() {
                        var deliveryCompany = $.trim($('input[name="delivery-company"]').val());
                        var deliveryNumber = $.trim($('input[name="delivery-number"]').val());
                        var cost = $.trim($('input[name="cost"]').val());
                        var moneyType = $.trim($('select[name="money-type"]').val());
                        var note = $.trim($('textarea[name="note"]').val());
                        var time = $.trim($('input[name="time"]').val());
                        $.ajax({
                            url: "/buy/changeShippingStatus",
                            type: "post",
                            data: 'recId=' + recId + '&actionValue=' + actionValue + '&orderId=' + orderId + '&deliveryCompany=' + deliveryCompany + '&deliveryNumber=' + deliveryNumber + '&cost=' + cost + '&moneyType=' + moneyType + '&note=' + note + '&time=' + time,
                            dataType: 'json',
                            success: function(data) {
                                if (data == 1) {
                                    alert('执行成功');
                                    window.location.reload();
                                    return true;
                                } else {
                                    alert('执行失败');
                                    return false;
                                }
                            }
                        });
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

    })
});

function showCheckedGoods() {
    var goods = [];
    $('input[name="goods_id"]:checked').each(function() {
        goods.push($.trim($(this).attr('goods-id')));
    })
    if (goods.length > 0) {
        var goodsStr = goods.join(",");
        return goodsStr;
    } else {
        alert('您尚未选择任何商品！');
        return false;
    }
}

function delAction(id) {
    $.ajax({
        url: "/buy/delShippingStatus",
        type: "post",
        data: 'id=' + id,
        dataType: 'json',
        success: function(data) {
            if (data.status) {
                alert('删除成功');
                window.location.reload();
                return true;
            } else {
                alert(data.msg);
                return false;
            }
        }
    });
}

function changeInput(obj, id, column) {
    var content = $.trim($(obj).html());
    if (content.indexOf("input") == -1 && content.indexOf("textarea") == -1) {
        if ($.trim(column) != 'note' && $.trim(column) != 'oversea_url') {
            var html = '<input type="text" style="width: 80px;margin: 0;height:28px;line-height:25px;" name="' + id + '_' + column + '" value="' + content + '" onblur="saveChangeInput(this,' + "'" + id + "'" + ',' + "'" + column + "'" + ')">';
            $(obj).html(html);
            $(obj).find('input').focus();
        } else {
            if (content == '/') {
                content = '';
            }
            var html = '<textarea style="width: 80%;margin: 0;line-height:25px;" name="' + id + '_' + column + '" onblur="saveChangeInput(this,' + "'" + id + "'" + ',' + "'" + column + "'" + ')">' + content + '</textarea>';
            $(obj).html(html);
            $(obj).find('textarea').focus();
        }
    }
}

function saveChangeInput(obj, id, column) {
    var cont = $.trim($(obj).val());
    if ($.trim(column) == 'note' || $.trim(column) == 'oversea_url') {
        if (cont.length == 0) {
            cont = '/';
        }
    }
    $.ajax({
        url: "/buy/updateBuyOrder",
        type: "post",
        data: 'id=' + id + '&column=' + column + '&cont=' + cont,
        dataType: 'json',
        success: function(data) {
            if (data.status === true) {
                $('#' + id + '_' + column).html(cont);
            } else {
                alert(data.message);
                return false;
            }
        }
    });
}

function changeShippingInput (obj, id, column) {
    var content = $.trim($(obj).html());
    if (content.indexOf("input") == -1 && content.indexOf("textarea") == -1) {
        if ($.trim(column) != 'note') {
            var html = '<input type="text" style="width: 80px;margin: 0;height:28px;line-height:25px;" name="' + id + '_' + column + '" value="' + content + '" onblur="saveChangeShippingInput(this,' + "'" + id + "'" + ',' + "'" + column + "'" + ')">';
            $(obj).html(html);
            $(obj).find('input').focus();
        } else {
            if (content == '/') {
                content = '';
            }
            var html = '<textarea style="width: 80%;margin: 0;line-height:25px; height:28px;" name="' + id + '_' + column + '" onblur="saveChangeShippingInput(this,' + "'" + id + "'" + ',' + "'" + column + "'" + ')">' + content + '</textarea>';
            $(obj).html(html);
            $(obj).find('textarea').focus();
        }
    }
}

function saveChangeShippingInput(obj, id, column) {
    var cont = $.trim($(obj).val());
    if ($.trim(column) == 'note') {
        if (cont.length == 0) {
            cont = '/';
        }
    }
    if ($.trim(column) == 'cost') {
        if (cont.length == 0) {
            cont = '0.00';
        }
    }
    $.ajax({
        url: "/buy/updateShippingStatus",
        type: "post",
        data: 'id=' + id + '&column=' + column + '&cont=' + cont,
        dataType: 'json',
        success: function(data) {
            if (data.status === true) {
                $('#shipping_' + id + '_' + column).html(cont);
            } else {
                alert(data.message);
                return false;
            }
        }
    });
}