<div class="col-md-12">
	<h3 style="text-align: center">لیست محصولات فروشده </h3>
	
	<table id="example" class="table table-striped table-bordered display" style="width:100%">
		<thead>
		<tr>
			<th>ادی</th>
			<th>قیمت بسته (افغانی)</th>
			<th>قیمت واحد (افغانی)</th>
			<th>قیمت کل (افغانی)</th>
			<th>تعداد</th>
			<th>تاریخ فروش</th>
			<th> عملیات</th>
		
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th>ادی</th>
			<th>قیمت بسته</th>
			<th>قیمت واحد</th>
			<th>قیمت کل</th>
			<th>تعداد</th>
			<th>تاریخ فروش</th>
			<th> عملیات</th>
		
		</tr>
		</tfoot>
		<tbody>
		@foreach($sale_products as $sale_product)
			
			<tr>
				<td>{{$sale_product->sale_id}}</td>
				<td>{{number_format($sale_product->package_price)}}</td>
				<td>{{number_format($sale_product->uniit_price)}}</td>
				<td>{{number_format($sale_product->total_price}}</td>
				<td>{{$sale_product->quantity}}</td>
				<td>{{$sale_product->sale_date}}</td>
				
				<td style="text-align: center"><a href="javascript:ajaxLoad('sale_product/update/{{$sale_product->sale_id}}')" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></a>
					<a href="javascript:if(confirm('Are you sure you want to delete this?')) ajaxDelete('{{route('sale_product.delete',$sale_product->sale_id)}}','{{csrf_token()}}')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
			</tr>
		
		@endforeach
		
		</tbody>
	</table>

</div>
