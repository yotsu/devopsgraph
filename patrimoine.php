<!-- Styles -->
<?php
require('process-results.php');
$patrimoineValue = 0;
$selectedPatrimoine = '';
if (isset($_GET['patrimoine'])) {
    $patrimoineValue = $_GET['patrimoine'];
    foreach ($patrimoine_notes as $patrimoine) {
        if ($patrimoine['patrimoine'] == $patrimoineValue) {
            $selectedPatrimoine = $patrimoine;
        }
    }
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="css/normalize.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<!-- Resources -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

<?php

?>

<!-- Chart code -->
<script>
    am4core.ready(function() {

// Themes begin
        am4core.useTheme(am4themes_animated);
// Themes end

        /* Create chart instance */
        var chart = am4core.create("chartdiv", am4charts.RadarChart);
        /* Add data */
        chart.data = [

        <?php
            $body = '';
            $body .= '
            {
                "category": "DevOps",
                "maturity": '.$selectedPatrimoine['devops'].'
            }, {
                "category": "Agile",
                "maturity": '.$selectedPatrimoine['agile'].'
            }, {
                "category": "Outils",
                "maturity": '.$selectedPatrimoine['tools'].'
            }, {
                "category": "Automatisation",
                "maturity": '.$selectedPatrimoine['automation'].'
            }, {
                "category": "Qualité",
                "maturity": '.$selectedPatrimoine['quality'].'
            }, {
                "category": "Sécurité",
                "maturity": '.$selectedPatrimoine['security'].'
            }';
            echo $body;
        ?>
        ];

        /* Create axes */
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "category";

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.axisFills.template.fill = chart.colors.getIndex(2);
        valueAxis.renderer.axisFills.template.fillOpacity = 0.05;

        /* Create and configure series */
        var series = chart.series.push(new am4charts.RadarSeries());
        series.dataFields.valueY = "maturity";
        series.dataFields.categoryX = "category";
        series.name = "Maturité DevOps";
        series.strokeWidth = 3;

        /* Add legend */
        chart.legend = new am4charts.Legend();

    }); // end am4core.ready()
</script>

<!-- HTML -->
<div class="header-container">
    <div>
        <h1 class="title">Maturité DevOps du patrimoine <?php echo get_patrimoine_name($selectedPatrimoine['patrimoine']); ?> - <?php echo ucfirst(return_date('m')); ?> 2020</h1>
        <h3 class="title"><a href="index.php">Retourner aux Départements</a></h3>
    </div>
</div>
<div class="main-container">
    <div id="chartdiv"></div>
</div>
