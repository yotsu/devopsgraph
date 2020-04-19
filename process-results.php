<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(TRUE);
$spreadsheet = $reader->load("results_sondage.xlsx");
$worksheet = $spreadsheet->getActiveSheet();
$patrimoine_notes = array();
$line = 0;
$statusValue = '1';

foreach($worksheet->getRowIterator() as $row)
{
    $line++;
    $statusValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $line)->getValue();
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
    if ($line >= 4 && $statusValue == 1) {
        $patrimoine = '';
        $dept = '';
        $patValue = '';
        $deptValue = '';
        $column = 0;
        $devops_sum = 0;
        $devops_divider = 0;
        $agile_sum=0;
        $agile_divider = 0;
        $tools_sum=0;
        $tools_divider = 0;
        $automation_sum=0;
        $automation_divider = 0;
        $quality_sum=0;
        $quality_divider = 0;
        $security_sum=0;
        $security_divider=0;
        foreach ($cellIterator as $cell) {
                if($column == 7) { //patrimoine
                    if (trim($cell->getValue()) !== '') {
//                        $patrimoine = get_patrimoine_name($cell->getValue());
//                        $dept = get_department_name($cell->getValue());
                        $patValue = $cell->getValue();
                        $deptValue = patrimoine_to_departement($patValue);
//                        print_r("departement : " . $deptValue . " | patrimoine : " . $patValue . "//////");
                    } else {
//                        $patrimoine = get_patrimoine_name(0);
//                        $dept = get_department_name(0);
                        $patValue = 0;
                        $deptValue = patrimoine_to_departement($patValue);
//                        print_r("departement : " . $deptValue . " | patrimoine : " . $patValue . "//////");
                    }
                }
                elseif ($column >= 8 && $column <= 12) { //grouping for DevOps
                    if (trim($cell->getValue()) !== '') {
                        $devops_sum = $devops_sum + note_conv($column, $cell->getValue());
                    } else {
                        $devops_sum = $devops_sum + 0;
                    }
                    $devops_divider++;
                }
                else if ($column >=13 && $column <= 18) { //grouping for Agile
                    if (trim($cell->getValue()) !== '') {
                        $agile_sum = $agile_sum + note_conv($column, $cell->getValue());
                    } else {
                        $agile_sum = $agile_sum + 0;
                    }

                    $agile_divider++;
                }
                else if ($column >= 19 && $column <= 22) { //grouping for Tools
                    if (trim($cell->getValue()) !== '') {
                        $tools_sum = $tools_sum + note_conv($column, $cell->getValue());
                    } else {
                        $tools_sum = $tools_sum + 0;
                    }

                    $tools_divider++;
                }
                else if ($column >= 23 && $column <= 24) { //grouping for Automation
                    if (trim($cell->getValue()) !== '') {
                        $automation_sum = $automation_sum + note_conv($column, $cell->getValue());
                    } else {
                        $automation_sum = $automation_sum + 0;
                    }

                    $automation_divider++;
                }
                else if ($column >= 25 && $column <= 30) { //grouping for Quality
                    if (trim($cell->getValue()) !== '') {
                        $quality_sum = $quality_sum + note_conv($column, $cell->getValue());
                    } else {
                        $quality_sum = $quality_sum + 0;
                    }

                    $quality_divider++;
                }
                else if ($column == 31 && strlen($cell->getValue()) > 15) { //extra resp in Quality
                    if (trim($cell->getValue()) !== '') {
                        $quality_sum = $quality_sum + note_conv($column, '1');
                    } else {
                        $quality_sum = $quality_sum + 0;
                    }

                    $quality_divider++;
                }
                else if ($column >= 32 && $column <= 34) { //grouping for Security
                    if (trim($cell->getValue()) !== '') {
                        $security_sum = $security_sum + note_conv($column, $cell->getValue());
                    } else {
                        $security_sum = $security_sum + 0;
                    }

                    $security_divider++;
                }
                else if ($column == 35 && strlen($cell->getValue()) > 15) { //extra resp in Security
                    if (trim($cell->getValue()) !== '') {
                        $security_sum = $security_sum + note_conv($column, '1');
                    } else {
                        $security_sum = $security_sum + 0;
                    }

                    $security_divider++;
                }
                $column++;
        }
        array_push($patrimoine_notes,
            array(
            'department' => $deptValue,
            'patrimoine' => $patValue,
            'devops' => return_mark($devops_sum, $devops_divider),
            'agile' => return_mark($agile_sum, $agile_divider),
            'tools' => return_mark($tools_sum, $tools_divider),
            'automation' => return_mark($automation_sum, $automation_divider),
            'quality' => return_mark($quality_sum, $quality_divider),
            'security' => return_mark($security_sum, $security_divider),
        ));

    }

}

//print_r(return_marks_by_departement($patrimoine_notes, 5));

function return_marks_by_departement($patrimoine_notes, $department) {

    $counter = 0;
    $exist = false;
    $devops_sum=0;
    $agile_sum=0;
    $tools_sum=0;
    $automation_sum=0;
    $quality_sum=0;
    $security_sum=0;

    foreach ($patrimoine_notes as $val) {
        if ($val['department'] == $department) {
            $exist = true;
            $counter++;
            $devops_sum = $devops_sum + $val['devops'];
            $agile_sum = $agile_sum + $val['agile'];
            $tools_sum = $tools_sum + $val['tools'];
            $automation_sum = $automation_sum + $val['automation'];
            $quality_sum = $quality_sum + $val['quality'];
            $security_sum = $security_sum + $val['security'];
        }
    }

    if ($exist) {
        return array(
        'department' => get_department_name($department),
        'devops' => round((($devops_sum / $counter) / 5), 1)*5,
        'agile' => round((($agile_sum / $counter) / 5), 1)*5,
        'tools' => round((($tools_sum / $counter) / 5), 1)*5,
        'automation' => round((($automation_sum / $counter) / 5), 1)*5,
        'quality' => round((($quality_sum / $counter) / 5), 1)*5,
        'security' => round((($security_sum / $counter) / 5), 1)*5,
    );

        //for debug
//        return array(
//            'department' => get_department_name($department),
//            'devops' => round((($devops_sum / $counter) / 5), 1)*5,
//            'agile' => round((($agile_sum / $counter) / 5), 1)*5,
//            'tools' => round((($tools_sum / $counter) / 5), 1)*5,
//            'automation' => round((($automation_sum / $counter) / 5), 1)*5,
//            'quality' => round((($quality_sum / $counter) / 5), 1)*5,
//            'security' => round((($security_sum / $counter) / 5), 1)*5,
//            'NbofPatrimoine' => $counter,
//            'DevOpsSum' => $devops_sum,
//            'aS' => $agile_sum,
//            'ts' => $tools_sum,
//            'as' => $automation_sum,
//            'qs' => $quality_sum,
//            'ss' => $security_sum
//        );
    }

}

function return_mark($sum, $divider) {
    return round((($sum / $divider) / 5), 1)*5;
}

function note_conv($col, $resp) {
    //devops
    $col8 = array(1 => 5, 2 => 3, 3=>0, 4=>1);
    $col9 = array(1 => 5, 2 => 0, 3=>1);
    $col10 = array(1 => 5, 2 => 0, 3=>1);
    $col11 = array(1 => 5, 2 => 4, 3=>2, 4=>1);
    $col12 = array(1 => 5, 2 => 4, 3=>3, 4=>1, 5=> 0);
    //agile col 13 à 18
    $col13 = array(1 => 5, 2 => 3, 3=>0, 4=>1);
    $col14 = array(1 => 5, 2 => 3, 3=>0, 4=>1);
    $col15 = array(1 => 5, 2 => 3, 3=>0, 4=>1);
    $col16 = array(1 => 5, 2 => 3, 3=>0, 4=>1);
    $col17 = array(1 => 5, 2 => 3, 3=>0, 4=>1);
    $col18 = array(1 => 5, 2 => 3, 3=>0, 4=>1);
    //tools col 19 à 22
    $col19 = array(1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>0);
    $col20 = array(1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>0);
    $col21 = array(1 => 1, 2 => 2, 3=>4, 4=>5, 5=>0);
    $col22 = array(1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>0);
    //automation col 23 à 24
    $col23 = array(1 => 0, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>1);
    $col24 = array(1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>0);
    //quality col 25 à 31
    $col25 = array(1 => 0, 2 => 1, 3=>3, 4=>5, 5=>2);
    $col26 = array(1 => 0, 2 => 1, 3=>3, 4=>5, 5=>2);
    $col27 = array(1 => 0, 2 => 1, 3=>3, 4=>5, 5=>2);
    $col28 = array(1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>0);
    $col29 = array(1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>0);
    $col30 = array(1 => 1, 2 => 2, 3=>3, 4=>4, 5=>5, 6=>0);
    $col31 = array(1 => 5);
    //security 32 à 35
    $col32 = array(1 => 0, 2 => 2, 3=>4, 4=>5, 5=>1);
    $col33 = array(1 => 0, 2 => 2, 3=>4, 4=>1, 5=>5);
    $col34 = array(1 => 1, 2 => 2, 3=>3, 4=>5, 5=>0);
    $col35 = array(1 => 5);

    $convtab = array(8 => $col8, 9 => $col9, 10 => $col10, 11 => $col11, 12 => $col12, 13=> $col13,14=> $col14,15=> $col15,16=> $col16,17=> $col17,18=> $col18,19=> $col19,20=> $col20,21=> $col21,22=> $col22,23=> $col23,24=> $col24,25=> $col25,26=> $col26,27=> $col27,28=> $col28,29=> $col29,30=> $col30,31=> $col31,32=> $col32,33=> $col33,34=> $col34,35=> $col35);

    return $convtab[$col][$resp];
}

function patrimoine_to_departement($resp) {
    $departement = array (
        '0'=>0,
        '1'=>1,
        '2'=>1,
        '3'=>2,
        '4'=>3,
        '5'=>3,
        '6'=>4,
        '7'=>5,
        '8'=>6,
        '9'=>6,
        '10'=>6,
        '11'=>6,
        '12'=>6,
        '13'=>6,
        '14'=>6,
        '15'=>6,
        '16'=>6,
        '17'=>7,
        '18'=>7,
        '19'=>8,
        '20'=>8,
        '21'=>8,
        '22'=>9,
        '23'=>9,
        '24'=>9,
        '25'=>10,
        '26'=>11,
        '27'=>11,
        '28'=>11,
        '29'=>12,
        '30'=>12,
        '31'=>13,
        '32'=>13,
        '33'=>13,
        '34'=>13,
        '35'=>13,
        '36'=>14,
        '37'=>14,
        '38'=>14,
        '39'=>14,
        '40'=>15,
        '41'=>15,
        '42'=>15,
        '43'=>15
    );

    return $departement[$resp];
}

function get_department_name($resp) {
    $departement = array (
        '0'=>'UNKNOWN',
        '1'=>'AEP',
        '2'=>'CARDIF Corporate',
        '3'=>'COMPTABILITE & FINANCE',
        '4'=>'DATAHUB',
        '5'=>'DIGITAL COURTAGE',
        '6'=>'DONNEES',
        '7'=>'DSI CARDIF',
        '8'=>'EPARGNE',
        '9'=>'GESTIONS DES ACTIFS',
        '10'=>'IT CORPORATE',
        '11'=>'PREVOYANCE',
        '12'=>'RISQUES & ACTUARIAT',
        '13'=>'SOCLES DIGITAUX',
        '14'=>'SOCLES TECHNIQUES & SUPPORT',
        '15'=>'TRANSVERSE',
    );

    return $departement[$resp];
}

function get_patrimoine_name($resp) {
    $patrimoine = array (
        '0' => 'UNKNOWN',
        '1'=>' Back-Office AEP',
        '2'=>' Transverse AEP',
        '3'=>' Portail',
        '4'=>' Finance core business',
        '5'=>' Finance data management',
        '6'=>' DataHub',
        '7'=>' Digital Courtage',
        '8'=>' Actuariat et analyse',
        '9'=>' Actuariat et inventaire',
        '10'=>' Datalab',
        '11'=>' Epargne et client analytics',
        '12'=>' GRC',
        '13'=>' Prevoyance Pilotage commercial',
        '14'=>' Référentiel',
        '15'=>' Reflet',
        '16'=>' Reglementaire analytics',
        '17'=>' Outils Mobiles & Digitaux',
        '18'=>' Portail',
        '19'=>' Coeur Epargne',
        '20'=>' Successions',
        '21'=>' Vente Epargne',
        '22'=>' Chaine d\'investissement',
        '23'=>' Front Office',
        '24'=>' Referentiel de l\'Actif',
        '25'=>' TSP',
        '26'=>' ADE',
        '27'=>' Indemnisation',
        '28'=>' Prevoyance individuelle',
        '29'=>' Actuariat',
        '30'=>' Risques',
        '31'=>' Applications de proximite',
        '32'=>' DISTRIBUTION Consultation Transverse',
        '33'=>' Outils Mobiles & Digitaux',
        '34'=>' Portail',
        '35'=>' Robotics Process Automation',
        '36'=>' Socles Asynchrones',
        '37'=>' Socles Mutualises',
        '38'=>' Socles Synchrones',
        '39'=>' Support et Outils du delivery',
        '40'=>' Commissions',
        '41'=>' Conformite',
        '42'=>' Documents',
        '43'=>' Outils du Collaborateur',
    );
    return $patrimoine[$resp];
}



date_default_timezone_set("Europe/Paris");

//fetches current date and time
$date = date("Y-m-d H:i:s");
$dateArray = date_parse_from_format('d/m/Y', $date);
$month = DateTime::createFromFormat('!m', $dateArray['month'])->format('F');
$dateString = $dateArray['day'] . " " . $month  . " " . $dateArray['year'];

$body =
    '<div class="header-container"><div>'.
    '<h1 class="title">Maturité DevOps -  '. $dateString . '</h1>' .
    '<h3 class="title">14 réponses sur 43 attendues au 07/04</h3>' .
    '</div></div><div class="main-container"><div id="chartdiv"></div></div>' .
    '<script>'.
    'am4core.useTheme(am4themes_animated);' .
    'var chart = am4core.create("chartdiv", am4charts.RadarChart);'.
    'chart.data = [';
    for ($i = 0; $i <= 15; $i++) {
        $notes = return_marks_by_departement($patrimoine_notes, $i);
        $body .= '
        {
        "department": ' . $notes['department'] . ',
        "devops": ' . $notes['devops'] . ',
        "agile": ' . $notes['agile'] . ',
        "tools": ' . $notes['tools'] . ',
        "automation": ' . $notes['automation'] . ',
        "quality": ' . $notes['quality'] . ',
        "security": ' . $notes['security'] . ',
        }';
    }
$body .= ']' .

'/* Create axes */
var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "department";
categoryAxis.renderer.labels.template.location = 0.5;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.renderer.labels.template.location = 1;
valueAxis.renderer.labels.template.verticalCenter = "bottom";
valueAxis.renderer.labels.template.fillOpacity = 0.5;
valueAxis.renderer.maxLabelPosition = 0.99; ' .

for ($i = 0; $i <= 15; $i++) {
    $notes = return_marks_by_departement($patrimoine_notes, $i);
    $body .= '
    /* Create and configure series */
    var series1 = chart.series.push(new am4charts.RadarColumnSeries());
    series1.dataFields.valueY = "devops";
    series1.dataFields.categoryX = "department";
    series1.name = "DevOps";
    series1.strokeWidth = 0;
    series1.columns.template.tooltipText = "Compétence: {name}\nDomaine: {categoryX}\nMaturité: {valueY}";
    series1.sequencedInterpolation = true;
    series1.sequencedInterpolationDelay = 100;
    series1.stacked = true;
    ';
}

$body .=
    'chart.legend = new am4charts.Legend();' .
    '//chart.cursor = new am4charts.RadarCursor();' .
    'chart.startAngle = -170;' .
    'chart.endAngle = -10;' .
    'chart.innerRadius = am4core.percent(50);' .
'</script>';