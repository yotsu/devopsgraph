<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(TRUE);
$spreadsheet = $reader->load("results_sondage.xlsx");

$worksheet = $spreadsheet->getActiveSheet();

$dept_notes = array();

$line = 0;

foreach($worksheet->getRowIterator() as $row) {
    $line++;
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,

        if ($line >= 4 ) {
            $patrimoine = '';
            $dept = '';
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
                    if($column == 7) {
                        if (trim($cell->getValue()) !== '') {
                            $patrimoine = get_patrimoine_name($cell->getValue());
                            $dept = get_department_name($cell->getValue());
                        } else {
                            $patrimoine = get_patrimoine_name(0);
                            $dept = get_department_name(0);
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
            array_push($dept_notes,
                array($dept =>
                    array($patrimoine =>
                        array(
                            'devops' => return_mark($devops_sum, $devops_divider),
                            'agile' => return_mark($agile_sum, $agile_divider),
                            'tools' => return_mark($tools_sum, $tools_divider),
                            'automation' => return_mark($automation_sum, $automation_divider),
                            'quality' => return_mark($quality_sum, $quality_divider),
                            'security' => return_mark($security_sum, $security_divider),
                        )
                    )
                )
            );
        }
}

//array_multisort($dept_notes[1], SORT_ASC, SORT_STRING);
print_r($dept_notes);


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

function get_department_name($resp) {
    $departement = array (
        '0'=>'UNKNOWN',
        '1'=>'AEP',
        '2'=>'AEP',
        '3'=>'CARDIF Corporate',
        '4'=>'COMPTABILITE & FINANCE',
        '5'=>'COMPTABILITE & FINANCE',
        '6'=>'DATAHUB',
        '7'=>'DIGITAL COURTAGE',
        '8'=>'DONNEES',
        '9'=>'DONNEES',
        '10'=>'DONNEES',
        '11'=>'DONNEES',
        '12'=>'DONNEES',
        '13'=>'DONNEES',
        '14'=>'DONNEES',
        '15'=>'DONNEES',
        '16'=>'DONNEES',
        '17'=>'DSI CARDIF',
        '18'=>'DSI CARDIF',
        '19'=>'EPARGNE',
        '20'=>'EPARGNE',
        '21'=>'EPARGNE',
        '22'=>'GESTIONS DES ACTIFS',
        '23'=>'GESTIONS DES ACTIFS',
        '24'=>'GESTIONS DES ACTIFS',
        '25'=>'IT CORPORATE',
        '26'=>'PREVOYANCE',
        '27'=>'PREVOYANCE',
        '28'=>'PREVOYANCE',
        '29'=>'RISQUES & ACTUARIAT',
        '30'=>'RISQUES & ACTUARIAT',
        '31'=>'SOCLES DIGITAUX',
        '32'=>'SOCLES DIGITAUX',
        '33'=>'SOCLES DIGITAUX',
        '34'=>'SOCLES DIGITAUX',
        '35'=>'SOCLES DIGITAUX',
        '36'=>'SOCLES TECHNIQUES & SUPPORT',
        '37'=>'SOCLES TECHNIQUES & SUPPORT',
        '38'=>'SOCLES TECHNIQUES & SUPPORT',
        '39'=>'SOCLES TECHNIQUES & SUPPORT',
        '40'=>'TRANSVERSE',
        '41'=>'TRANSVERSE',
        '42'=>'TRANSVERSE',
        '43'=>'TRANSVERSE'
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