$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function addNewProductAttributeItem() {
    let $productAttributeCount = isNaN($(".productSubAttribute").last().attr("data-index")) ? 0 : parseInt($(".productSubAttribute").last().attr("data-index")) + 1;
    let $html = '<div class="form-row productSubAttribute" data-index="' + $productAttributeCount + '">\n' +
        '                                <div class="form-group col-md-9">\n' +
        '                                    <label for="exampleInputEmail1">Başlık</label>\n' +
        ' <input type="hidden" name="productSubAttributeTitleHidden' + $productAttributeCount + '" value="0">\n' +
        '                       <input type="text" class="form-control" name="productSubAttributeTitle' + $productAttributeCount + '">\n' +
        '                                </div>\n' +
        '                                <div class="form-group col-md-3">\n' +
        '                                    <label for="exampleInputEmail1">&nbsp;</label><br>\n' +
        '                                    <a href="javascript:void(0);" onclick="deleteProductSubAttributeFromForm(' + $productAttributeCount + ')"><i\n' +
        '                                            class="fa fa-trash text-red"></i></a>\n' +
        '                                </div>\n' +
        '                            </div>'
    $("#productSubAttributeContainer").append($html);
}


function deleteProductSubAttributeFromDB($id, $index) {
    if (confirm('bu alt özellik silinecektir onayluyor musunuz ?')) {
        $.post({
            url: "/admin/product/attributes/deleteSubAttribute/" + $id + "",
            dataType: 'json',
            success: function (data) {
                if (data === "true") {
                    deleteProductSubAttributeFromForm($index);
                } else {
                    alert(data);
                }
            }
        })
    }
}

function deleteProductSubAttributeFromForm($index) {
    $("#productSubAttributeContainer .productSubAttribute[data-index=" + $index + "]").fadeOut(300, function () {
        this.remove();
    });
}
