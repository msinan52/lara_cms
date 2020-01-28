// alert dialog gizlemek için
setTimeout(function () {
    $('.alert-success').slideUp(500);
}, 10000);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


function productVariantAttributeOnChange(productId) {
    let productDetailSelectedAttributes = $("#productDetailsContainer .productVariantAttribute");
    let selectedAttributeIdList = [];
    $.each(productDetailSelectedAttributes, function (index, element) {
        if ($("#" + element.id + " option:selected").attr('data-value') !== undefined)
            selectedAttributeIdList.push($("#" + element.id + " option:selected").attr('data-value'));
    });
    $.ajax({
        url: '/getProductVariantPriceAndQtyWithAjax',
        data: {
            'productId': productId,
            'selectedAttributeIdList': selectedAttributeIdList
        },
        success: function (data) {
            if (data !== false) {
                $('span.price').text(data.price);
                $('span.qty').text(data.qty);
                $("#qty").attr('dt-max', data.qty).val(1)
            } else {
                $('span.price').text($("#productDefaultPrice").val());
                $('span.qty').text($("#productDefaultQty").val());
            }
        }
    })
}


function addItemToBasket($productId, $hasProductDetail) {
    if ($hasProductDetail == 1) {
        $("#productQuickView" + $productId + "").click();
    } else {
        var variantCount = $("#productDetailsContainer .productVariantAttribute").length === undefined ? 1 : $("#productDetailsContainer .productVariantAttribute").length;
        var selectedVariants = getProductDetailSelectedAttributeList();
        var selectedVariantCount = selectedVariants.length;
        if (selectedVariantCount < variantCount) {
            alert("Lütfen ürüne ait tüm özellikleri seçiniz")
        } else {
            $.post('/sepet/addToBasket', {
                id: $productId,
                selectedAttributeIdList: getProductDetailSelectedAttributeList(),
                qty: $("input#qty").val()
            }, function (data, status) {
                if (data.status.status === true) {
                    basketItemAddToHtml(data.card, data.cardPrice)
                    $(".mfp-close").click();
                    $(".dropdown-cart-action").removeClass('d-lg-none');
                    $("#openShoppingCart").click();
                } else {
                    alert(data.status.message)
                }
            })
        }
    }
}

function removeBasketItem(elem) {
    rowId = $(elem).attr('data-value');
    $.post('/sepet/removeBasketItem', {
        rowId: rowId
    }, function (data, status) {
        if (status === "success") {
            basketItemAddToHtml(data.card, data.cardPrice)
            $("#openShoppingCart").click();
        }
    })
}

function getProductDetailSelectedAttributeList() {
    var selectedAttributeList = [];
    $.each($("#productDetailsContainer .productVariantAttribute"), function (index, element) {
        if ($(element).val() !== "")
            selectedAttributeList.push($(element).val())
    });
    return selectedAttributeList;
}


function basketItemAddToHtml(card, cardPrice) {
    var item = '';
    var basketContainer = $("#basketContainer");
    var itemCount = 0;
    basketContainer.html('');
    Object.keys(card).forEach(function (key) {
        var value = card[key];
        itemCount += parseInt(value['qty']);
        item = ' <div class="product">\n' +
            '  <div class="product-details">\n' +
            '      <h4 class="product-title">\n' +
            '          <a href="/urun/' + value['options']['slug'] + '">' + value['name'] + '</a>\n' +
            '      </h4>\n' +
            '\n' +
            '      <span class="cart-product-info">\n' +
            '              <span class="cart-product-qty">' + value['qty'] + '</span>\n' +
            '              x <span class="cart-product-price">' + value['price'] + ' ₺</span>\n' +
            '             <br> <span class="small">' + value['options']['attributeText'] + '</span>\n' +
            '          </span>\n' +
            '  </div><!-- End .product-details -->\n' +
            '\n' +
            '  <figure class="product-image-container">\n' +
            '      <a href="/urun/' + value['options']['slug'] + '" class="product-image">\n' +
            '          <img width="80" height="80" src="/uploads/products/' + value['options']['image'] + '"\n' +
            '               alt="' + value['title'] + '">\n' +
            '      </a>\n' +
            '      <a href="#" class="btn-remove" title="Ürünü kaldır"  onclick="return removeBasketItem(this)" data-value="' + value['rowId'] + '"><i class="icon-cancel"></i></a>\n' +
            '  </figure>\n' +
            ' </div>';
        $("#basketContainer").append(item);
    });
    $(".cart-count").text(itemCount);
    $("span.cart-total-price").text(cardPrice)
    // $("#basketContainer").html(items);
}

function addToFavorites(productId) {
    $.post('/favoriler/ekle', {
        productId: productId
    }, function (data, status) {
        if (data == "true") {
            alert("Favorilere Eklendi")
        }
    })
}

function getSelectedBrandList() {
    var brandList = new Array();
    var index = 0;

    $($("#productBrandUl li")).each(function () {
        var itemList = [];
        jQuery(this).find(".activeBrand").each(function () {
            if ($(this).attr('data-value') != null) {
                brandList.push(parseInt($(this).attr('data-value')));
            }
        });
        index++;
    });
    return brandList;
}

function getAttrList() {
    var attrList = new Array();
    var index = 0;

    $($("div[id*='attrFilterByID']")).each(function () {
        var itemList = [];
        jQuery(this).find(".activeSubAttribute").each(function () {
            itemList.push(parseInt($(this).attr('value')));
        });
        attrList[index] = new Array(itemList);
        index++;
    });
    return attrList;
}

$('input#q').autocomplete({
    source: function (request, response) {
        $.ajax({
            type: 'GET',
            url: '/headerSearchBarOnChangeWithAjax',
            dataType: "json",
            cache: true,
            success: function (data) {
                var array = $.map(data, function (item) {
                    return {
                        label: item.title,
                        value: item.id
                    }
                });
                response($.ui.autocomplete.filter(array, request.term));
            }
        });
    },
    create: function () {
        $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
            return $("<li>")
                .append("<span>" + item.label + "</span>")
                .appendTo(ul);
        };
    }, select: function (event, ui) {
        if (ui.item.value !== 0) {
            window.location.href = "/ara?q=&cat=" + ui.item.value
        }
    }
});
