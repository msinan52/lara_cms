function addToNewSupportCar() {
    var containerUyumluAraclar = $("#containerUyumluAraclar");
    var totalSupportCarCount = $(".itemUyumluAraclar").length
    var currentSupportCarCount = totalSupportCarCount + 1;
    var appendHtml = '<div class="box-body itemUyumluAraclar">\n' +
        '                 <div class="form-group col-md-2 ">\n' +
        '                      <label>Marka</label>\n' +
        '                      <select class="form-control chc_marka" onchange="chcMarkaOnChange(' + currentSupportCarCount + ')" name="uyumluaraclar_set-' + currentSupportCarCount + '-parent_marka">\n' +
        '                         <option value="0">--- Marka SeÃ§iniz ---</option>\n' +
        '                      </select>\n' +
        '                    </div>\n' +
        '                    <div class="form-group col-md-2 ">\n' +
        '                      <label>Model</label>\n' +
        '                      <select class="form-control chc_model" onchange="chcModelOnChange(' + currentSupportCarCount + ')" name="uyumluaraclar_set-' + currentSupportCarCount + '-parent_model">\n' +
        '                         <option value="0">--- Model SeÃ§iniz ---</option>\n' +
        '                      </select>\n' +
        '                    </div>\n' +
        '                    <div class="form-group col-md-2 ">\n' +
        '                      <label>Kasa</label>\n' +
        '                      <select class="form-control chc_kasa" onchange="chcKasaOnChange(' + currentSupportCarCount + ')" name="uyumluaraclar_set-' + currentSupportCarCount + '-parent_kasa">\n' +
        '                         <option value="0">--- Kasa SeÃ§iniz ---</option>\n' +
        '                      </select>\n' +
        '                    </div>\n' +
        '                    <div class="form-group col-md-2 ">\n' +
        '                      <label>YÄ±l</label>\n' +
        '                      <select class="form-control chc_yil" onchange="chcModelYiliOnChange(' + currentSupportCarCount + ')"  name="uyumluaraclar_set-' + currentSupportCarCount + '-parent_model_yili">\n' +
        '                        <option value="0">--- Model Yili SeÃ§iniz ---</option>\n' +
        '                      </select>\n' +
        '                    </div>\n' +
        '                    <div class="form-group col-md-2 ">\n' +
        '                      <label>Motor Hacmi</label>\n' +
        '                      <select class="form-control chc_motor_hacmi" onchange="chcMotorHacmiOnChange(' + currentSupportCarCount + ')"  name="uyumluaraclar_set-' + currentSupportCarCount + '-parent_motor_hacmi">\n' +
        '                         <option value="0">--- Motor Hacmi SeÃ§iniz ---</option>\n' +
        '                      </select>\n' +
        '                    </div>\n' +
        '                    <div class="form-group col-md-2 ">\n' +
        '                      <label>Beygir GÃ¼cÃ¼</label>\n' +
        '                      <select class="form-control chc_beygir_gucu"  name="uyumluaraclar_set-' + currentSupportCarCount + '-parent_beygir_gucu">\n' +
        '                        <option value="0">--- Beygir GÃ¼cÃ¼ SeÃ§iniz ---</option>\n' +
        '                      </select>\n' +
        '                    </div>\n' +
        '                </div>'
    containerUyumluAraclar.append(appendHtml);
    bindAllCarModels($("select[name='uyumluaraclar_set-" + currentSupportCarCount + "-parent_marka']"));
}

function bindAllCarModels(bindingElement) {
    $.ajax({
        url: '/brands/getAllActiveCarBrands',
        dataType: 'json',
        data: {},
        success: function (data) {
            $(data).each(function (index, element) {
                $('<option />', {
                    'value': element.slug,
                    'text': element.title,
                    'class': element.slug
                }).appendTo(bindingElement);
            });
        }
    })
}

function bindAllYedekParcaMarka(bindingElement) {
    $.ajax({
        url: '/getAllActiveCarBrands',
        dataType: 'json',
        data: {},
        success: function (data) {
            $(data).each(function (index, element) {
                $('<option />', {
                    'value': element.title,
                    'text': element.title
                }).appendTo(bindingElement);
            });
        }
    })
}

function addToNewProperty() {
    var containerOzellikler = $("#containerOzellikler");
    var totalAttrCount = $(".itemOzellikler").length
    var currentAttrCount = totalAttrCount + 1;
    var appendHtml =
        '<div class="box-body itemOzellikler"  id="productPropertyContainer' + totalAttrCount + '">\n' +
        '     <div class="form-group col-md-6">\n' +
        '       <label>Başlık</label>\n' +
        '          <input type="text" class="form-control" name="properties[' + currentAttrCount + '][key]" placeholder="Özellik Adı" value="">\n' +
        '           </div>\n' +
        '           <div class="form-group col-md-5 ">\n' +
        '       <label>Açıklama</label>\n' +
        '     <input type="text" class="form-control" name="properties[' + currentAttrCount + '][value]" placeholder="Açıklama" value="">\n' +
        '   </div>\n' +
        ' <div class="form-group col-md-1">\n' +
        '                                            <label>Sil</label><br>\n' +
        '                                            <a onclick="deleteProductProperties(' + totalAttrCount + ')"><i class="fa fa-trash"></i></a>\n' +
        '</div>'
    ' </div>'
    containerOzellikler.append(appendHtml);
}

function deleteProductProperties(index) {
    $("#productPropertyContainer" + index).remove();
}

function deleteProductOem(index) {
    $("#productOemContainer" + index).remove();
}

function addToNewOem() {
    var containerOzellikler = $("#containerOem");
    var totalAttrCount = $(".itemOem").length
    var currentAttrCount = totalAttrCount + 1;
    var appendHtml =
        '<div class="box-body itemOem" id="productOemContainer'+currentAttrCount+'">\n' +
        '    <div class="form-group col-md-6 ">\n' +
        '                                                    <label>Marka</label>\n' +
        '                                                    <select class="form-control" name="oems[' + currentAttrCount + '][key]">\n' +
        '                                                        <option value="">--- Parça Marka Seçiniz ---</option>\n' +
        '                                                    </select>\n' +
        '                                                </div>\n' +
        '                                                <div class="form-group col-md-5">\n' +
        '                                                    <label for="exampleInputEmail1">Oem Kodu</label>\n' +
        '                                                    <input type="text" class="form-control" name="oems[' + currentAttrCount + '][value]" placeholder="Oem Kodu ">\n' +
        '  </div>\n' +
        '    <div class="form-group col-md-1">\n' +
        '                                                <label>Sil</label><br>\n' +
        '                                                <a onclick="deleteProductOem(' + currentAttrCount + ')"><i class="fa fa-trash"></i></a>\n' +
        '                                            </div>\n' +
        '   </div>';
    containerOzellikler.append(appendHtml);
    bindAllYedekParcaMarka($("select[name='oems[" + currentAttrCount + "][key]']"));
}

function bindAllSubCats(kendi, appendToElementId) {
    $.ajax({
        url: '/home/getAllAltKategori',
        dataType: 'json',
        data: {
            'id': kendi
        },
        success: function (data) {
            $("#subCat option").not(":first").remove()
            $(JSON.parse(data.models)).each(function (index, element) {
                $('<option />', {
                    'value': element.pk,
                    'text': element.fields.title,
                }).appendTo($("#" + appendToElementId + ""));
            });
        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    })
}

function removeImageById(kendi) {
    $.ajax({
        url: '/magaza/admin/urun/removeImage/',
        dataType: 'json',
        data: {
            'itemId': kendi,
            'className': "Files",
        },
        success: function (data) {
            var sonuc = JSON.parse(data.models)
            console.log(sonuc)
            if (sonuc == true) {
                $("#divProductGalleryItem" + kendi + "").hide(1000)
            } else {
                alert("resim silinirken bir hata oluÅŸtu")
            }

        }
    })
}

function deleteProduct(kendi) {
    var txt;
    var r = confirm("ÃœrÃ¼n Silme Ä°ÅŸlemi ! \n \n eÄŸer bu Ã¼rÃ¼nÃ¼ silerseniz Ã¼rÃ¼nle ilgili bÃ¼tÃ¼n nesneleri silmiÅŸ olacaksÄ±nÄ±z \n Silmek istediÄŸine emin misin ?");
    if (r == true) {
        $.ajax({
            url: '/magaza/admin/urun/urun-sil/',
            dataType: 'json',
            data: {
                'productId': kendi,
            },
            success: function (data) {
                var sonuc = JSON.parse(data.models)
                console.log(sonuc)
                if (sonuc == true) {
                    $("#product-item-" + kendi + "").hide(1000)
                } else {
                    alert("resim silinirken bir hata oluÅŸtu")
                }

            }
        })
    } else {
        txt = "You pressed Cancel!";
    }

}
