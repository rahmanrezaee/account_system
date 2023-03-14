<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}">

    <title>Document</title>
    <style>

		
		
			@media print {
		
		@page {
			margin:20px;
			
		}
		body{
			
			margin: auto
		}
		
		.col-md-12
	
	
	}
	
	
    </style>
	
</head>
<body  style="height: 410mm;margin: 0 auto">
<div class="container" style="height: 100%">
    <div class="row" style="height: 100%">

        <table style="direction: rtl;font-family: Sahel !important; height: 100%"
               class="table table-responsive table-bordered">
            <tr style='text-align: center' class="fill-content-color-black text-center-own">
                <td colspan="4">
                    <img style="margin-bottom: -15px;margin-top: -30px;height: 100px" src="{{ asset("assets/images/fator_logo.png") }}" alt="">
                    <h2>
					
					فاکتـــــور قصـــر امپـــــراطور
					(دیکوریشن)
					</h2>
                      <span>
					شماره های تماس
					-
					0790200004-
					0785243024-
					0780524000-
					0799102100-
					
					</span>
                </td>
            </tr>

            <tr style="border-top:3px solid black;border-bottom:3px solid black;height: 40px">

                <td> نام و تخلص :
                    {{ $res_decore->name }}
                </td>

                <td>
                    شماره تلفون:{{ $res_decore->phone }}
                </td>
                <td colspan="1">
				
					آدرس :{{ $res_decore->address }}
                </td>
                <td colspan="1"> نمبر مسلسل :{{ $res_decore->res_hall_id }}
                </td>
            </tr>

            <tr  style="height: 40px">
                <td colspan="4" class="fill-content-color-black text-center-own">
                    <span class="text-xxlarge"> فرمایشات</span>
                </td>
            </tr>


            <tr>

                <td colspan="4" class="bordered no-padding-own table-bordered-own">
                    <table class="table table-bordered table-responsive " style="margin-bottom:-25px;height: 90px">
                        <tbody>
                        <tr>

                             <td>
                                تاریخ ثبت
                            :
							</td>
                            <td class="day-date">
                                {{ $res_decore->register_date }}

                            </td>
                            <td>تاریخ محفل:</td>
                            <td class="celebrate-date">

                                {{ $res_decore->date_reserve }}
                            </td>
                            <td>ایام هفته:</td>
                            <td class="day-week">

                                {{ $res_decore->week_day }}
                            </td>
                        </tr>

                        <tr>
                            <td>زمان شروع :</td>
                            <td class="start-time">

                                {{ $res_decore->start_time }}

                            </td>
                            <td>شب / روز :</td>
                            <td class="time-celerb">

                                {{ $res_decore->day_night }}
                            </td>

                        </tr>


                        </tbody>
                    </table>
                </td>
            </tr>
            <tr  style="height: 40px">
                <td colspan="4" class="fill-content-color-black text-center-own">
                    <span class="text-xxlarge"> سالون ها</span>
                </td>
            </tr>
            <tr>


                <td colspan="4">

                    <div class="row ">
                        @foreach($list as $li => $value)
                            <div class="col-md-4  col-lg-4 col-sm-4 col-xs-4 " style="padding:5px;float:right">
                                <div class="col-md-12 text-center">
                                    {{ explode("|",$li)[0] }}
                                </div>
                                <hr>
                                <div class="col-md-12 " style="padding:5px">
                                    <?php

                                    $count = count($value);

                                    for ($i = 0; $i < $count;$i++) {?>

                                    <div class="col-md-12 table-bordered" style="text-align: justify-all;padding:5px;">
                                        <?php echo($value[$i]->dname); ?>

                                    </div>

                                    <?php
                                    }

                                    ?>

                                </div>
                            </div>
                        @endforeach

                    </div>



                </td>

            </tr>

            <tr>
                <td colspan="1">
                    <div class="col-md-12">
                        <table class="table table-bordered">

                            <tr>
                                <td>پرداخت فعلی :</td>
                                <td id="current-pay">{{ $res_decore->current_payment }}</td>
                            </tr>
                            <tr>
                                <td>الباقی :</td>
                                <td id="remine-pay"></td>
                            </tr>
                            <tr>
                                <td>مجموعه :</td>
                                <td id="total-pay">{{ $res_decore->total_payment }}</td>
                            </tr>


                        </table>

                    </div>
                </td>

                <td colspan="3" style="height: 200px;text-align: center;padding-top: 44px;">

                    امضاء مسئول تالار
                    <span style="margin-right: 100px">امضاء فرمایش دهنده
                    </span>


                </td>

            </tr>
        </table>

    </div>
</div>


<script>


    var  v = document.getElementById("current-pay").innerHTML;
    var f = document.getElementById("total-pay").innerHTML;


    // var food = document.getElementsByClassName("type-foot")[0].innerHTML;
    // document.getElementById("foot-type-name").innerHTML = " ( "+food+" ) ";



    var re = parseInt(f)- parseInt(v);

    document.getElementById("remine-pay").innerHTML = re;

    window.print();
    window.close();

</script>
</body>
</html>