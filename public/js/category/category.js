layui.use('form', function () {
    var form = layui.form
        , layer = layui.layer;
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

    form.on("select(type)", function (data) {
        var category_type = data.value;
        var category_level = parseInt(category_type) + 1;
        $("[name=category_level]").val(category_level);
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type: 'post',
            url: "/admin/category/getHierarchyCategory",
            data: {category_type: category_type},
            dataType: 'json',
            success: function (result) {
                // console.log($("[name=parent_id]"));
                var str = "";
                var str1 = "<option value='0'>请选择</option>";

                if (result.length != 0) {
                    $.each(result, function (k, v) {
                        str += "<option value='" + v['id'] + "'>" + v['category_name'] + "</option>";
                    });
                    $("#category").html(str1 + str);
                    form.render('select');
                } else {
                    $("[name=parent_id]").html(str1);
                    form.render('select');
                }
            }
        });

    })
    form.on("select(level1)", function (data) {
        var category_type = data.value;
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type: 'post',
            url: "/admin/common/getCategoryInfo",
            data: {parent_id: category_type},
            dataType: 'json',
            success: function (result) {
                var str = "";
                var str1 = "<option value='0'>请选择</option>";

                if (result.length != 0) {
                    $.each(result, function (k, v) {
                        str += "<option value='" + v['id'] + "'>" + v['category_name'] + "</option>";
                    });
                    $("#level2").html(str1 + str);
                    $("#level3").html(str1);
                    form.render('select');
                } else {
                    $("#level2").html(str1);
                    $("#level3").html(str1);
                    form.render('select');
                }
            }
        });

    });
    form.on("select(level2)", function (data) {
        var category_type = data.value;
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type: 'post',
            url: "/admin/common/getCategoryInfo",
            data: {parent_id: category_type},
            dataType: 'json',
            success: function (result) {
                var str = "";
                var str1 = "<option value='0'>请选择</option>";

                if (result.length != 0) {
                    $.each(result, function (k, v) {
                        str += "<option value='" + v['category_name'] + "'>" + v['category_name'] + "</option>";
                    });
                    $("#level3").html(str1 + str);
                    form.render('select');
                } else {
                    $("#level3").html(str1);
                    form.render('select');
                }
            }
        });

    });

});