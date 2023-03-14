<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factor</title>
    <link rel="stylesheet" href="{{ asset('assets/plugin/bootstrap/css/bootstrap.min.css') }}">
    <style>

        .bold{
            font-weight: bold;
        }

        .dir-rtl {
            direction: rtl !important;
        }

        .no-bordered, .no-bordered tr, .no-bordered td {
            border: none !important;
        }

        .sign {
            height: 100px;
        }

        .logo {
            width: 25%;
            margin-bottom: 5px;
        }

        th {

            text-align: center;
        }

        .no-bordered tr, .no-bordered td {
            padding: 3px !important;
        }

        .flex-grow-1 {
            flex-grow: 1;
        }
        .none-product-border{

            border-bottom: none !important;
            border-top: none !important;
            padding-bottom: 5px !important;
            padding-top: 5px !important;

        }
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{

            padding: 0 5px !important;
        }

        .flex-row-reverse {
            display: flex;
            flex-direction: row;
        }
        .d-flex{
            display: flex;
        }
        .justyfiy-content-space-between{
            justify-content: space-between;
        }
        .margin-bottom-30{
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="width-100  text-right">
        <img class="logo" src="{{ asset(get_options('CompanyLogo')->value('option_value')) }}">
    </div>
    <br>
    <div class="d-flex justyfiy-content-space-between margin-bottom-30">

        <div>
            <span>{{ get_options("companyName")->value('option_value') }} , {{ get_options("ownerAddress")->value('option_value') }} </span><br>
            {{--<span><b>Raika Bäckerei Gb</b></span><br>--}}
            {{--<span><b>Siemensstr. 6 </b></span><br>--}}
            <span><b>{{ get_options("ownerAddress")->value('option_value') }}</b></span><br>
            {{--<span><b>Deutschland</b></span><br>--}}
        </div>






        <div  class="dir-rtl">
            <span><b>  {{ get_options("companyName")->value('option_value') }}</b></span><br>
            <span><b>  {{ get_options("ownerName")->value('option_value') ." ". get_options("ownerFamily")->value('option_value')  }}</b></span><br>

            <span><b>{{ get_options("ownerAddress")->value('option_value') }}</b></span><br>
            <span><b> Tel.: {{  get_options("ownerPhone")->value('option_value') }}</b></span><br>
            <span><b> Email Adresse : {{ get_options("ownerEmail")->value('option_value') }}</b></span><br>
            {{--<span><b> Datum: {{  }}</b></span><br>--}}

        </div>



    </div>
    {{--<div class="d-flex justyfiy-content-space-between">--}}
    {{--<div>--}}
    {{--<span><b> Jaghuri Food Im-/Export</b></span><br>--}}
    {{--<span><b>  Siemens Straße 6</b></span><br>--}}
    {{--<span><b>64289 Darmstadt</b></span><br>--}}
    {{--<span><b> Tel. 0049 157 311 38 434</b></span><br>--}}
    {{--<span><b> Email Adresse : j_sarwar_m2010@yahoo.com</b></span><br>--}}

    {{--</div>--}}

    {{--<div style="direction: ltr">--}}
    {{--<span>Nadi Holding GmbH · Schnackenbugallee 149c · 22525 Hamburg</span><br>--}}
    {{--<span><b>Raika Bäckerei Gb</b></span><br>--}}
    {{--<span><b>Siemensstr. 6 </b></span><br>--}}
    {{--<span><b>64289 Darmstadt</b></span><br>--}}
    {{--<span><b>Deutschland</b></span><br>--}}
    {{--</div>--}}


    {{--</div>--}}

    <div class=" d-flex justyfiy-content-space-between margin-bottom-30">

        <div class="align-items-center d-flex"><h2>Rechnung Nr.  {{ $fp->sale_factor_code }}</h2></div>
        <div class="col-md-4">
            <span class=" bold">Seite:</span><span class="pull-right bold">{{  ceil(count($pl) / 10) }}</span><br>
            <span class=" bold">Kunden Nr.:</span><span  class="pull-right bold">{{ $fp->customer_code }}</span><br>
            {{--<span class=" bold">Zu Lieferschein Nr.:</span><span  class="pull-right bold">1225</span><br>--}}
            <span class=" bold">Lieferdatum:</span><span  class="pull-right bold"> {{ ($fp->sale_date) }}</span><br>
            <span class=" bold">Datum:</span><span  class="pull-right bold"> {{ ($fp->sale_date) }}</span><br>

        </div>
    </div>


    <table class="table table-bordered  ">

        <tr>
            <th>Pos</th>
            <th colspan="2"> Menge </th>
            <th>Art-Nr.</th>
            <th>Text </th>
            <th>Einzelpreis EUR</th>
            <th>USt. %</th>
            <th>Gesamtpreis EUR</th>
        </tr>

        @foreach($pl as $key => $p)

            <tr>

                <td class="none-product-border"> {{ $key+1 }} </td>
                <td class="none-product-border">{{ $p->quantity }}</td>
                <td class="none-product-border">{{ get_unit($p->unit_id)->value('unit_name') }}</td>
                <td class="none-product-border">{{ $p->product_code }}</td>
                <td class="none-product-border">{{ $p->product_name }}</td>
                <td class="none-product-border">{{ ($p->sale_price) }}</td>
                <td class="none-product-border">{{ ($p->tex_precentage) ." % " }}</td>
                <td class="none-product-border">{{ $p->total_price }}</td>
            </tr>
        @endforeach

        <tfoot class="dir-rtl text-right">
        <tr>
            <th colspan="7" class="text-left">
                Gesamt Netto
            </th>
            <th style="direction: ltr">{{ $fp->total_price }} € </th>


        </tr>
        <tr>
            <th colspan="7" class="text-left"> <span> : MwSt</span> <span class="pull-right">  </span></th>
            <th colspan="" style="direction: ltr">{{ ($fp->total_tex) }} € </th>
        </tr>
        <tr>
            <th colspan="7" class="text-left"><b>Gesamtbetrag</b></th>
            <th colspan="" style="direction: ltr">{{ $fp->total_price }} € </th>
        </tr>


        </tfoot>
    </table>

    <p>Zahlbar sofort netto ohne Abzug</p>
    <p>Rechnungsdatum entspricht dem Leistungsdatum</p>
    <br>
    <div class="text-center" style="">

        {!! get_options("FooterContentFactor")->value('option_value') !!}

    </div>

</div>
</div>


<script>

    window.print();
    setTimeout(window.close,1000);

</script>
</body>


</html>