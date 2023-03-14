<div class="row" id="content">
	<div class="col col-md-12">
		<img src="/images/header.png" alt="">
	</div>
	{{--<div class="col col-md-12 factor_information" id="printale" >
		<div class="col col-md-4 header_right">
			<div class="left col col-md-4">
				<label for="name">نام مشتری:</label><br>
				<label for="name">ایمل:</label><br>
				<label for="name">ادرس مشتری:</label><br>
				<label for="name">نمبر تماس:</label><br>
				<label for="name">تاریخ خریداری:</label>
			</div>
			<div class="right col col-md-6">
				<label for="name">بلال احمدی</label><br>
				<label for="name">balal.ahmadi@yahoo.com</label><br>
				<label for="name">هرات شهر نو جاده 64 متره</label><br>
				<label for="name">0787367284</label><br>
				<label for="name">1397/2/23</label>
			
			</div>
		
		</div>
		<div class="col col-md-4 header_middle" style="text-align: center">
			<label for="factor" class="factor">فکتورخریداجناس</label><br>
			<label for="factor" class="factor">نمبرفکتور(10234)</label><br>
		
		</div>
		<div class="col col-md-4 header_left" style="float: left">
			<div class="col col-md-4 right" >
				<label for="factor" class="sale_factor">تخفیف:</label><br>
				<label for="factor" class="sale_factor" >مجموع کل:</label><br>
			
			</div>
			<div class="col col-md-4 left" >
				<label for="factor">8.77</label><br>
				<label for="factor">9868273</label><br>
			
			</div>
		
		</div>
	
	</div>--}}
	<div class="col col-md -12">
		
		
		<table id="example" class="table table-striped table-bordered display" style="width:100%;direction: rtl">
			<div class="row">
				<div class="col col-md-12 factor_information" id="title">
					<div class="col col-md-4 header_right">
						<div class="left col col-md-4">
							<label for="name">نام مشتری:</label><br>
							<label for="name">ایمل:</label><br>
							<label for="name">ادرس مشتری:</label><br>
							<label for="name">نمبر تماس:</label><br>
							<label for="name">تاریخ خریداری:</label>
						</div>
						<div class="right col col-md-6">
							<label for="name">بلال احمدی</label><br>
							<label for="name">balal.ahmadi@yahoo.com</label><br>
							<label for="name">هرات شهر نو جاده 64 متره</label><br>
							<label for="name">0787367284</label><br>
							<label for="name">1397/2/23</label>
						
						</div>
					
					</div>
					<div class="col col-md-4 header_middle" style="text-align: center">
						<label for="factor" class="factor">فکتورخریداجناس</label><br>
						<label for="factor" class="factor">نمبرفکتور(10234)</label><br>
					
					</div>
					<div class="col col-md-4 header_left" style="float: left">
						<div class="col col-md-4 right">
							<label for="factor" class="sale_factor">تخفیف:</label><br>
							<label for="factor" class="sale_factor">مجموع کل:</label><br>
						
						</div>
						<div class="col col-md-4 left">
							<label for="factor">8.77</label><br>
							<label for="factor">9868273</label><br>
						
						</div>
					
					</div>
				
				</div>
			</div>
			<thead>
			<tr>
				<th>نام جنس</th>
				<th>کود نمبر</th>
				<th>تعداد</th>
				<th>قیمت</th>
				<th>توضیحات</th>
				<th>مجموعه</th>
			</tr>
			</thead>
			
			<tbody>
			@foreach($factors as $factor)
				<tr>
					<td>{{$factor->product_name}}</td>
					<td>{{$factor->product_code}}</td>
					<td>{{$factor->product_quantity}}</td>
					<td>{{$factor->product_price}}</td>
					<td>{{$factor->product_total}}</td>
				
				</tr>
			@endforeach
			
			
			</tbody>
		</table>
	</div>
	<div class="col col-md-12">
		<img src="/images/footer.jpg" alt="" width="100%">
	
	</div>

</div>

{{--<script src="/js/jQuery.print.js"></script>--}}

<script type="text/javascript">

    $(document).ready(function () {
        $('#example').DataTable({
            dom: 'Bfrtip',

            buttons: [
                {
                    extend: 'print',
                    customize: function (win) {
                        $(win.document.body)
                            .css('font-size', '10pt')
                            .prepend(
                                '<div class="row" style="direction: rtl">' +
                                '\t\t\t<div class="col col-md-12 factor_information" id="title" >\n' +
                                '\t\t\t\t<div class="col col-md-4 header_right">' +
                                '\t\t\t\t\t<div class="left col col-md-4">' +
                                '\t\t\t\t\t\t<label for="name">نام مشتری:</label><br>' +
                                '\t\t\t\t\t\t<label for="name">ایمل:</label><br>' +
                                '\t\t\t\t\t\t<label for="name">ادرس مشتری:</label><br>' +
                                '\t\t\t\t\t\t<label for="name">نمبر تماس:</label><br>' +
                                '\t\t\t\t\t\t<label for="name">تاریخ خریداری:</label>' +
                                '</div>' +
                                '<div class="right col col-md-6">' +
                                '\t\t\t\t\t\t<label for="name">بلال احمدی</label><br>' +
                                '\t\t\t\t\t\t<label for="name">balal.ahmadi@yahoo.com</label><br>' +
                                '\t\t\t\t\t\t<label for="name">هرات شهر نو جاده 64 متره</label><br>' +
                                '\t\t\t\t\t\t<label for="name">0787367284</label><br>' +
                                '\t\t\t\t\t\t<label for="name">1397/2/23</label>' +
                                '\t\t\t\t\t\n' +
                                '\t\t\t\t\t</div>\n' +
                                '\t\t\t\t\n' +
                                '\t\t\t\t</div>\n' +
                                '\t\t\t\t<div class="col col-md-4 header_middle" style="text-align: center">\n' +
                                '\t\t\t\t\t<label for="factor" class="factor">فکتورخریداجناس</label><br>\n' +
                                '\t\t\t\t\t<label for="factor" class="factor">نمبرفکتور(10234)</label><br>\n' +
                                '\t\t\t\t\n' +
                                '\t\t\t\t</div>\n' +
                                '\t\t\t\t<div class="col col-md-4 header_left" style="float: left">\n' +
                                '\t\t\t\t\t<div class="col col-md-4 right" >\n' +
                                '\t\t\t\t\t\t<label for="factor" class="sale_factor">تخفیف:</label><br>\n' +
                                '\t\t\t\t\t\t<label for="factor" class="sale_factor" >مجموع کل:</label><br>\n' +
                                '\t\t\t\t\t\n' +
                                '\t\t\t\t\t</div>\n' +
                                '\t\t\t\t\t<div class="col col-md-4 left" >\n' +
                                '\t\t\t\t\t\t<label for="factor">8.77</label><br>\n' +
                                '\t\t\t\t\t\t<label for="factor">9868273</label><br>\n' +
                                '\t\t\t\t\t\n' +
                                '\t\t\t\t\t</div>\n' +
                                '\t\t\t\t\n' +
                                '\t\t\t\t</div>\n' +
                                '\t\t\t\n' +
                                '\t\t\t</div>\n' +
                                '\t\t\t</div>'
                            );

                        $(win.document.body).find('#title')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]
        });
    });

    /*    $(function() {
	
			$("#data_test").find('.print').on('click', function () {
	
				$("#data_test").print({
	
	
					globalStyles: false,
	
	// Add link with attrbute media=print
	
					mediaPrint: false,
	
					stylesheet: "http://fonts.googleapis.com/css?family=Inconsolata",
	
					iframe: false,
	
					noPrintSelector: ".avoid-this",
	
					append: "Free jQuery Plugins<br/>",
	
					prepend: "<br/>jQueryScript.net",
	
					manuallyCopyFormValues: true,
	
	
					deferred: $.Deferred(),
	
					timeout: 250,
	
					title: null,
					
	
					doctype: '<!doctype html>'
	
				});
	
			});
			});*/


</script>