<div class="col-md-12">
    <h4 style="text-align: center">
        {{isset($panel_title) ? $panel_title :''}}
    </h4>
    <div class="row margin-bottom-10">
        <div class="col-md-2">
            <div class="form-group">
                <select class="list-page form-control" >
                    <option>select count</option>
                    <option>5</option>
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="input-group">
                <button id="print_button"  class="fa fa-print form-control" ></button>
            </div>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-3">
            <div class="input-group">
                <input type="text" class="form-control" id="search" placeholder="Search" autocomplete="off">
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-search" id="search_icon"></i>
                </div>
            </div>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >شماره</th>
            <th>فروشگاه فرستنده</th>
            <th>فروشگاه دریافت کننده</th>
            <th>اسم محصول</th>
            <th>مقدار محصول</th>
            <th>تاریخ</th>
            <th>توضیحات</th>
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>


        </tbody>
    </table>

</div>
<script>
    $(document).ready(function () {
        $('#print_button').on('click',function(){
            window.print()
        })

        $(".list-page").change(function () {

            console.log();
            ajaxLoad('<?php echo $route; ?>'+"/"+$(this).val())

        })

        $(".pagination a").unbind().bind("click",function (event) {
            event.preventDefault();

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');

            var myurl = $(this).attr('href');

            ajaxLoad(myurl);


        })

        // search section
        $('#search_icon').click(function () {
            var search = $('#search').val();

            if (search != '') {
                $.ajax({
                    type: 'get',
                    url: 'transfer_product_money/search/'+search,
                    success: function (response) {
                        var trHTML = '';

                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.first_money_eq_id + '</td><td>' +
                                item.equipment_name + '</td><td>'+
                                item.money_amount + '</td><td>'+
                                item.date + '</td><td>'+
                                item.description + '</td><td id="operations">'+
                                '<button  id-item=' + item.first_money_eq_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_transfer_product" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.first_money_eq_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_transfer_product" ></button>' +
                                '</td>'
                            '</tr>';

                        });
                        $('#content-display').html(trHTML)
                    }

                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_transfer_product", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("transfer_product/update/" + id);
            });

            // Delete transfer_product
            $("#content-display").on('click', ".delete_transfer_product", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxLoad("transfer_product/delete/" + id);
                }
            });

        })
    })
</script>

