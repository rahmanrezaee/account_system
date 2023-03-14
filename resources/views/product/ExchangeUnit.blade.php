<div class="col-md-12">
    <h4 style="text-align: center">
        {{isset($panel_title) ? $panel_title :''}}
    </h4>

    <form method="post" id="frm"
          action="{{ route('UnitExchange.createAndUpdate')}}">

        {{csrf_field()}}
        <div class="form-row">

            <div class="col-md-12 table-responsive">

                <table class="table table-bordered table-striped autocomplete_table" style="margin-bottom: 8px;"
                       id="autocomplete_table">
                    <thead>
                    <tr>
                        <td>حذف</td>

                        <th> مقدار</th>
                        <th>یونیت مورد نظر</th>
                        <th>مقدار</th>
                        <th>یونیت اصلی</th>
                    </tr>
                    </thead>

                    <tbody>



                    @if(count($unitExchanges) > 0)


                        @foreach($unitExchanges as $key => $p)

                            <tr id="row_{{ $key+1 }}">
                                <td>
                                    <button type="button" id="delete_{{ $key+1 }}" class=" btn btn-danger btn-sm remove delete_row"><i
                                                class="glyphicon glyphicon-remove-sign"></i></button>
                                </td>

                                <td>
                                    <input type="text" data-field-name="quentity"  value="{{ $p->quentity }}" name="quentity[]"
                                           id="quentity_{{ $key+1 }}" class="form-control input-sm autocomplete_txt quentity">
                                </td>

                                <td>
                                    <select class="form-control input-sm relate_unit_id" id="relate_unit_{{ $key+1 }}"
                                            name="relate_unit_id[]"
                                            data-field-name="relate_unit_id">
                                        @foreach($units as $unit)
                                            <option @if($p->relate_unit_id == $unit->unit_id) selected @endif value="{{ $unit->unit_id }}" >{{ $unit->unit_name }}</option>
                                        @endforeach

                                    </select>
                                </td>

                                <td><input type="text"  data-field-name="main_quentity" name="main_quentity[]"
                                           id="main_quentity_{{ $key+1 }}" value="{{ $p->main_quentity }}" class="form-control input-sm  autocomplete_txt main_quentity ">
                                </td>
                                <td>
                                    <input type="hidden" value="{{ $p->unit_exchanger_id }}" name="id_before[]" >

                                    <select class="form-control input-sm main_unit" id="main_unit_{{ $key+1 }}"
                                            name="main_unit[]" data-field-name="main_unit">
                                        @foreach($units as $unit)
                                            <option @if($p->main_unit_id == $unit->unit_id) selected @endif value="{{ $unit->unit_id }}" >{{ $unit->unit_name }}</option>
                                        @endforeach
                                        {{--<option value="{{ $p->main_currency_id }}"  >{{ $mainCurrency->currency_name }}</option>--}}

                                    </select>
                                </td>


                            </tr>

                        @endforeach


                    @else

                        <tr id="row_1">
                            <td>
                                <button type="button" id="delete_1" class=" btn btn-danger btn-sm remove delete_row"><i
                                            class="glyphicon glyphicon-remove-sign"></i></button>
                            </td>


                            <td><input type="text" data-field-name="quentity" name="quentity[]"
                                       id="quentity_1" class="form-control input-sm autocomplete_txt quentity">
                            </td>
                            <td>
                                <select class="form-control input-sm relate_unit_id" id="relate_unit_id_1"
                                        name="relate_unit_id[]"
                                        data-field-name="relate_unit_id">
                                    @foreach($units as $unit) <option  value="{{ $unit->unit_id }}" >{{ $unit->unit_name }} </option>@endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" data-field-name="main_quentity" name="main_quentity[]" id="main_quentity_1" class="form-control input-sm  autocomplete_txt money_amount">
                            </td>

                            <td>

                                <select class="form-control input-sm main_unit_1"  id="main_unit_1"  name="main_unit[]" data-field-name="main_unit">
                                    @foreach($units as $unit) <option   value="{{ $unit->unit_id }}"  >{{ $unit->unit_name }}</option> @endforeach
                                </select>
                            </td>


                        </tr>


                    @endif


                    </tbody>
                    <tfoot>
                    <tr>
                        <td style="border: none;" colspan="5">
                            <button type="button" class=" btn btn-primary btn-sm  " title="اضافه نمودن سطر جدید"
                                    id="addNew">
                                <i class="glyphicon glyphicon-plus-sign"></i>
                            </button>
                        </td>

                    </tr>
                    </tfoot>
                </table>
                <ul id="pr_ul">
                    <li>
                        <button type="submit" id="btn-save-and-print" class="btn btn-primary">
                            ذخیره و پرنت
                        </button>

                    </li>


                </ul>

            </div>
        </div>
    </form><!--</form>-->


</div> <!--</row>-->
<script src="/js/jquery-ui.min.js"></script>

<script src="/js/printPreview.js"></script>
<!--<script src="/js/jquery.print-preview.js"></script> -->
<script src="/js/jQuery.print.js"></script>


<script type="text/javascript">


    $(document).ready(function () {
        var rowcount, addBtn, tableBody, mount;

        addBtn = $('#addNew');
        rowcount = $('#autocomplete_table tbody tr').length + 1;
        tableBody = $('#autocomplete_table');

        function formHtml() {
            //rowcount=rowcount+1;
            console.log(rowcount);
            html = '<tr id="row_' + rowcount + '">';
            html += '<td><button type="button" id="delete_' + rowcount + '"   class=" btn btn-danger btn-sm remove delete_row"><i class="glyphicon glyphicon-remove-sign"></i></button></td>';
            html += '<td><input type="number" data-field-name="quentity" name="quentity[]" id="quentity_1" class="form-control input-sm  autocomplete_txt quentity  "></td>';
            html += "<td><select class='form-control input-sm relate_unit_id' id='relate_unit_id_" + rowcount + "' name='relate_unit_id[]' data-field-name='relate_unit_id_" + rowcount + "'>@foreach($units as $unit)<option value='{{ $unit->unit_id }}'>{{ $unit->unit_name }}</option>@endforeach</select></td>";
            html += '<td><input type="number" data-field-name="main_quentity" name="main_quentity[]" id="main_quentity_' + rowcount +'" class="form-control input-sm autocomplete_txt main_quentity"></td>';
            html += "<td><select class='form-control input-sm main_unit'  id='main_unit_" + rowcount + "' name='main_unit[]' data-field-name='main_unit" + rowcount + "'>@foreach($units as $unit)<option value='{{ $unit->unit_id }}' >{{ $unit->unit_name }}</option>@endforeach</select></td>";
            html += '</tr>';
            rowcount++;
            return html;

        }

        function addNewRow() {
            var html = formHtml();
            console.log(html);
            tableBody.append(html);
        }

        function delete_Row() {

            var rowNo;
            id = $(this).attr('id');
            //console.log(id);
            id_arr = id.split("_");
            // console.log(id_arr);
            rowNo = id_arr[id_arr.length - 1];
            if (rowNo > 1) {
                $('#row_' + rowNo).remove();

            } else {
                alert("شما نمی توانداین سطر را جذف کندید");
            }

            //console.log($(this).parent());
            // $(this).parent().parent().remove();

        }

        function getId() {
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1];
        }


        function registerEvent() {
            addBtn.on('click', addNewRow);
            $(document).on('click', '.delete_row', delete_Row);

        }

        registerEvent();


    });
    $(document).ready(function () {

        selectTwo();
        jalali();

    });


</script>
