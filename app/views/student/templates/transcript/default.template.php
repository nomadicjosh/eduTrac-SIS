<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/*
Template Name: Default
Template Slug: default
*/

/**
 * Default Transcript View
 *  
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/blank');
$app->view->block('blank');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// create new PDF document
$pdf = new \app\src\tcpdf\Tcpdf('landscape', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);

// set default header data
$pdf->SetHeaderData('', '', _escape($stuInfo[0]['Level']) . ' Transcript', '', '', '');

// set header and footer fonts
$pdf->setHeaderFont([ 'freesans', '', PDF_FONT_SIZE_MAIN ]);
$pdf->setFooterFont([ 'freesans', '', PDF_FONT_SIZE_DATA ]);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, "20", PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin("12");
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// ---------------------------------------------------------

// set font
$pdf->SetFont('freesans', '', 8);

// add a page
$pdf->AddPage();

// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

// set some text for student info
$txt1 = get_name(_escape($stuInfo[0]['stuID'])) . "<br />";
$txt1 .= _escape($stuInfo[0]['address1']) . ' ' . _escape($stuInfo[0]['address2']) . "<br />";
$txt1 .= _escape($stuInfo[0]['city']) . ' ' . _escape($stuInfo[0]['state']) . ' ' . _escape($stuInfo[0]['zip']) . "<br />";

// set some text for student info
$txt2 = _t( 'Student ID: ' ) . (_escape($stuInfo[0]['altID']) != '' ? _escape($stuInfo[0]['altID']) : _escape($stuInfo[0]['stuID'])) . "<br />";
if(_escape($stuInfo[0]['ssn']) > 0) {
	$txt2 .= _t( 'Social Security #: ' ) . _escape($stuInfo[0]['ssn']) . "<br />";
} else {
	$txt2 .= _t( 'Social Security #: ' ) . "<br />";
}
if(_escape($stuInfo[0]['graduationDate']) > '0000-00-00') {
	$txt2 .= _t( 'Graduation Date: ' ) . _escape($stuInfo[0]['graduationDate']) . "<br />";
} else {
	$txt2 .= _t( 'Graduation Date: ' ) . "<br />";
}

// writeHTMLCell
$pdf->writeHTMLCell(0, 0, '', '', $txt1, 0, 1, 0, true, 'L', true);
$pdf->writeHTMLCell(0, 0, 234, 20, $txt2, 0, 1, 0, true, 'L', true);

// column titles
$table = '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped" id="table-example">';
$table .= '<thead><tr>';
$table .= '<th><b>'._t( 'Course' ).'</b></th>';
$table .= '<th><b>'._t( 'Course Title' ).'</b></th>';
$table .= '<th><b>'._t( 'Grade' ).'</b></th>';
$table .= '<th><b>'._t( 'Attempted Credits' ).'</b></th>';
$table .= '<th><b>'._t( 'Completed Credits' ).'</b></th>';
$table .= '<th><b>'._t( 'Grade Points' ).'</b></th>';
$table .= '<th><b>'._t( 'Term' ).'</b></th>';
$table .= '</tr></thead>';
$table .= '<tbody>';

if(_escape($transferGPA[0]['Attempted']) != NULL) {
foreach($transferCourses as $key => $value) {
    $table .= '<tr>';
    $table .= '<td>'._escape($value['CourseName']).' *</td>';
    $table .= '<td>'._escape($value['shortTitle']).'</td>';
    $table .= '<td>'._escape($value['grade']).'</td>';
    $table .= '<td>'._escape($value['attCred']).'</td>';
    $table .= '<td>'._escape($value['compCred']).'</td>';
    $table .= '<td>'._escape($value['gradePoints']).'</td>';
    $table .= '<td>'._escape($value['termCode']).'</td>';
    $table .= '</tr>';
}
}

if(_escape($transferGPA[0]['Attempted']) != NULL) {
$table .= '<tr>';
$table .= '<td colspan="3"><b>'._t( 'Transfer Cum. Totals' ).'</b></td>';
$table .= '<td>CRED.ATT = '._escape($transferGPA[0]['Attempted']).'</td>';
$table .= '<td>CRED.CPT = '._escape($transferGPA[0]['Completed']).'</td>';
$table .= '<td style="width:.13em;">GRD.PTS = '._escape($transferGPA[0]['Points']).'</td>';
$table .= '<td>GPA = '.floor((_escape($transferGPA[0]['GPA'])*100))/100 . '</td>';
$table .= '</tr>';
}

if(_escape($transferGPA[0]['Attempted']) != NULL) {
$table .= '<tr><td>&nbsp;</td></tr>';
}

foreach($courses as $k => $v) {
     $table .= '<tr>';
	if(_escape($v['creditType']) == 'TR') {
		$table .= '<td>'._escape($v['CourseName']).' *</td>';
	} else {
		$table .= '<td>'._escape($v['CourseName']).'</td>';
	}
     $table .= '<td>'._escape($v['shortTitle']).'</td>';
     $table .= '<td>'._escape($v['grade']).'</td>';
     $table .= '<td>'._escape($v['attCred']).'</td>';
     $table .= '<td>'._escape($v['compCred']).'</td>';
     $table .= '<td>'._escape($v['gradePoints']).'</td>';
     $table .= '<td>'._escape($v['termCode']).'</td>';
     $table .= '</tr>';
	 /*$table .= '<tr>';
	 $table .= '<td colspan="3"><b>'._t( 'Term Totals' ).'</b></td>';
	 $table .= '<td>CRED.ATT = '._escape($v['termAttCred']).'</td>';
	 $table .= '<td>CRED.CPT = '._escape($v['termCompCred']).'</td>';
	 $table .= '<td style="width:.13em;">GRD.PTS = '._escape($v['Points']).'</td>';
	 $table .= '<td>GPA = '.floor((_escape($v['termGPA'])*100))/100 . '</td>';
	 $table .= '</tr>';*/
}

$table .= '<tr>';
$table .= '<td colspan="3"><b>'._t( 'Cum. Totals' ).'</b></td>';
$table .= '<td>CRED.ATT = '._escape($tranGPA[0]['Attempted']).'</td>';
$table .= '<td>CRED.CPT = '._escape($tranGPA[0]['Completed']).'</td>';
$table .= '<td style="width:.13em;">GRD.PTS = '._escape($tranGPA[0]['Points']).'</td>';
$table .= '<td>GPA = '.floor((_escape($tranGPA[0]['GPA'])*100))/100 . '</td>';
$table .= '</tr>';
 
$table .= '</tbody>';
$table .= '</table>';

$pdf->writeHTML($table, true, 0);

$footer = "<p>***************************************************************************************************************************************************************************************************************************************************</p>";
$footer .= '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped" id="table-example">';
$footer .= '<thead><tr>';
$footer .= '<th><b>'._t( 'Degree' ).'</b></th>';
$footer .= '<th><b>'._t( 'Major' ).'</b></th>';
$footer .= '<th><b>'._t( 'Minor' ).'</b></th>';
$footer .= '<th><b>'._t( 'Specialization' ).'</b></th>';
$footer .= '<th><b>'._t( 'CCD' ).'</b></th>';
$footer .= '</tr></thead>';

$footer .= '<tbody>';
$footer .= '<tr>';
if(_escape($stuInfo[0]['graduationDate']) > '0000-00-00') {
$footer .= '<td>'._escape($stuInfo[0]['degreeCode']).' - ' . _escape($stuInfo[0]['degreeName']) . ' Awarded on ' . _escape($stuInfo[0]['graduationDate']) . '</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_escape($stuInfo[0]['majorCode']) != 'NULL') {
$footer .= '<td>'._escape($stuInfo[0]['majorCode']).' - '._escape($stuInfo[0]['majorName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_escape($stuInfo[0]['minorCode']) != 'NULL') {
$footer .= '<td>'._escape($stuInfo[0]['minorCode']).' - '._escape($stuInfo[0]['minorName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_escape($stuInfo[0]['specCode']) != 'NULL') {
$footer .= '<td>'._escape($stuInfo[0]['specCode']).' - '._escape($stuInfo[0]['specName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_escape($stuInfo[0]['ccdCode']) != 'NULL') {
$footer .= '<td>'._escape($stuInfo[0]['ccdCode']).' - '._escape($stuInfo[0]['ccdName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

$footer .= '</tr>';
$footer .= '</tbody>';
$footer .= '</table>';
$footer .= "<p>***************************************************************************************************************************************************************************************************************************************************</p>";
$footer .= "<p>*"._t( 'Transfer Credits' )."</p>";

$pdf->writeHTML($footer, true, 0);

$txt3 = 'Printed on ' . \Jenssegers\Date\Date::now()->format("m/d/Y @ h:i A");    

 // print a block of text using Write()
$pdf->Write($h=0, $txt3, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

// ---------------------------------------------------------

/*$pdf->Button('print', 30, 10, 'Print', 'Print()', array('lineWidth'=>2, 'borderStyle'=>'beveled', 'fillColor'=>array(128, 196, 255), 'strokeColor'=>array(64, 64, 64)));

// Form validation functions
$js = <<<EOD
function Print() {
    print();
}
EOD;

// Add Javascript code
$pdf->IncludeJS($js);*/

// close and output PDF document
$pdf->Output('transcript.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
$app->view->stop();