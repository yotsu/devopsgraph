<?php
    require('process-results.php')
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

    <!-- Resources -->
    <script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/charts.js"></script>
    <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

</head>

<div class="header-container">
    <div>
        <h1 class="title">Maturité DevOps - <?php echo ucfirst(return_date('m')); ?> 2020</h1>
        <h3 class="title"><?php echo count_answers(); ?> réponses sur 43 attendues au <?php echo return_date(null); ?></h3>
    </div>
</div>
<div class="main-container">
    <div id="chartdiv"></div>
</div>

<script>

    /* Set themes */
    am4core.useTheme(am4themes_animated);

    /* Create chart instance */
    var chart = am4core.create("chartdiv", am4charts.RadarChart);

    /* Add data */
    chart.data = [
    <?php
        $body = '';
        for ($i = 0; $i <= 15; $i++) {
            $notes = return_marks_by_departement($patrimoine_notes, $i);
            if (isset($notes)) {
                $body .= '
                {
                "department": "' . $notes['department'] . '",
                "devops": ' . $notes['devops'] . ',
                "agile": ' . $notes['agile'] . ',
                "tools": ' . $notes['tools'] . ',
                "automation": ' . $notes['automation'] . ',
                "quality": ' . $notes['quality'] . ',
                "security": ' . $notes['security'];
                if ($i == 15) {
                    $body .= '}';
                } else {
                    $body .= '},';
                }
            }
        }
        echo $body;
    ?>
    ];

    /* Create axes */
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "department";
    categoryAxis.renderer.labels.template.location = 0.5;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.renderer.labels.template.location = 1;
    valueAxis.renderer.labels.template.verticalCenter = "bottom";
    valueAxis.renderer.labels.template.fillOpacity = 0.5;
    valueAxis.renderer.maxLabelPosition = 0.99;

    /* Create and configure series */
    <?php
    $chartSeries = array('devops', 'agile', 'tools', 'automation', 'quality', 'security');
    $chartSeriesCounter = 0;
    $body = '';
    foreach ($chartSeries as $series) {
        $chartSeriesCounter++;
        $body .=
            'var series' .$chartSeriesCounter. ' = chart.series.push(new am4charts.RadarColumnSeries());'.
            'series'.$chartSeriesCounter.'.dataFields.valueY = "'.$series.'";'.
            'series'.$chartSeriesCounter.'.dataFields.categoryX = "department";'.
            'series'.$chartSeriesCounter.'.name = "'.$series.'";'.
            'series'.$chartSeriesCounter.'.strokeWidth = 0;'.
            'series'.$chartSeriesCounter.'.columns.template.tooltipText = "Compétence: {name}\nDomaine: {categoryX}\nMaturité: {valueY}";'.
            'series'.$chartSeriesCounter.'.sequencedInterpolation = true;'.
            'series'.$chartSeriesCounter.'.sequencedInterpolationDelay = 100;'.
            'series'.$chartSeriesCounter.'.stacked = true;';
    }
    echo $body;
    ?>

    /* Add legend */
    chart.legend = new am4charts.Legend();

    /* Add cursor */
    //chart.cursor = new am4charts.RadarCursor();

    /* Make chart angled */
    chart.startAngle = -170;
    chart.endAngle = -10;
    chart.innerRadius = am4core.percent(50);
</script>