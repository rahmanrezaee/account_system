<div class="row" class="col-md-12 col-sm-12" style="margin: 2%; margin-bottom: 10%">
    <div class="row">
        <div class="col-md-6 col-sm-6" style="height: 90%;">
            <h4>چارت سهم شرکا</h4>
            <canvas id="percentage"></canvas>
        </div>
        <div class="col-md-6 col-sm-6" style="height: 90%;">
            <h4>چارت مصارف </h4>
            <canvas id="expance"></canvas>
        </div>
    </div>
    <hr>
    <div class="row">

        <h4>گزارشات عواید</h4>
        <canvas id="myChart" height="120px"></canvas>
        <div/>


    </div>

</div>

<script src="{{ asset("assets/plugin/chart.js/Chart.bundle.min.js") }}"></script>
<script>

    $(document).ready(function () {

        var ctx = document.getElementById('myChart');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    "حمل",
                    "ثور",
                    "جوزا",
                    "سرطان",
                    "اسد",
                    "سنبله",
                    "میزان",
                    "عقرب",
                    "قوس",
                    "جدی",
                    "دلو",
                    "حوت",
                ],
                datasets: [{
                    label: '',
                    data: {!! json_encode($income) !!} ,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });


        var ctx = document.getElementById('percentage');

        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($lable_precentage) !!},
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor:
                        [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    data: {!! json_encode($data_precentage) !!}
                }]
            },
            options: {
                title: {
                    display: false,

                }
            }
        });


        /*--------------------------------EarnUp--------------------------------------*/

        new Chart(document.getElementById("expance"), {
            type: 'bar',
            data: {
                labels:
                    [

                        "حمل",
                        "ثور",
                        "جوزا",
                        "سرطان",
                        "اسد",
                        "سنبله",
                        "میزان",
                        "عقرب",
                        "قوس",
                        "جدی",
                        "دلو",
                        "حوت",

                    ]
                ,
                datasets: [
                    {
                        label: "Population (millions)",
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        data: {!!  json_encode($arr2)  !!}
                    }
                ]
            },
            options: {
                legend: {display: false},
                title: {
                    display: false,

                }
            }
        });

    });


</script>



