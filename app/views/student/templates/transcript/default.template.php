<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/*
Template Name: Default
Template Slug: default
*/

/**
 * Default Transcript View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/blank');
$app->view->block('blank');

// create new PDF document
$pdf = new \app\src\tcpdf\Tcpdf('landscape', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);

// set default header data
$pdf->SetHeaderData('', '', _h($stuInfo[0]['Level']) . ' Transcript', '', '', '');

// set header and footer fonts
$pdf->setHeaderFont([ PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ]);
$pdf->setFooterFont([ PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ]);

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
$pdf->SetFont('times', '', 8);

// add a page
$pdf->AddPage();

// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

// set some text for student info
$txt1 = get_name(_h($stuInfo[0]['stuID'])) . "<br />";
$txt1 .= _h($stuInfo[0]['address1']) . ' ' . _h($stuInfo[0]['address2']) . "<br />";
$txt1 .= _h($stuInfo[0]['city']) . ' ' . _h($stuInfo[0]['state']) . ' ' . _h($stuInfo[0]['zip']) . "<br />";

// set some text for student info
$txt2 = _t( 'Student ID: ' ) . _h($stuInfo[0]['stuID']) . "<br />";
if(_h($stuInfo[0]['ssn']) > 0) {
	$txt2 .= _t( 'Social Security #: ' ) . _h($stuInfo[0]['ssn']) . "<br />";
} else {
	$txt2 .= _t( 'Social Security #: ' ) . "<br />";
}
if(_h($stuInfo[0]['graduationDate']) > '0000-00-00') {
	$txt2 .= _t( 'Graduation Date: ' ) . _h($stuInfo[0]['graduationDate']) . "<br />";
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

if(_h($transferGPA[0]['Attempted']) != NULL) {
foreach($transferCourses as $key => $value) {
    $table .= '<tr>';
    $table .= '<td>'._h($value['CourseName']).' *</td>';
    $table .= '<td>'._h($value['shortTitle']).'</td>';
    $table .= '<td>'._h($value['grade']).'</td>';
    $table .= '<td>'._h($value['attCred']).'</td>';
    $table .= '<td>'._h($value['compCred']).'</td>';
    $table .= '<td>'._h($value['gradePoints']).'</td>';
    $table .= '<td>'._h($value['termCode']).'</td>';
    $table .= '</tr>';
}
}

if(_h($transferGPA[0]['Attempted']) != NULL) {
$table .= '<tr>';
$table .= '<td colspan="3"><b>'._t( 'Transfer Cum. Totals' ).'</b></td>';
$table .= '<td>CRED.ATT = '._h($transferGPA[0]['Attempted']).'</td>';
$table .= '<td>CRED.CPT = '._h($transferGPA[0]['Completed']).'</td>';
$table .= '<td style="width:.13em;">GRD.PTS = '._h($transferGPA[0]['Points']).'</td>';
$table .= '<td>GPA = '.floor((_h($transferGPA[0]['GPA'])*100))/100 . '</td>';
$table .= '</tr>';
}

if(_h($transferGPA[0]['Attempted']) != NULL) {
$table .= '<tr><td>&nbsp;</td></tr>';
}

foreach($courses as $k => $v) {
     $table .= '<tr>';
	if(_h($v['creditType']) == 'TR') {
		$table .= '<td>'._h($v['CourseName']).' *</td>';
	} else {
		$table .= '<td>'._h($v['CourseName']).'</td>';
	}
     $table .= '<td>'._h($v['shortTitle']).'</td>';
     $table .= '<td>'._h($v['grade']).'</td>';
     $table .= '<td>'._h($v['attCred']).'</td>';
     $table .= '<td>'._h($v['compCred']).'</td>';
     $table .= '<td>'._h($v['gradePoints']).'</td>';
     $table .= '<td>'._h($v['termCode']).'</td>';
     $table .= '</tr>';
	 /*$table .= '<tr>';
	 $table .= '<td colspan="3"><b>'._t( 'Term Totals' ).'</b></td>';
	 $table .= '<td>CRED.ATT = '._h($v['termAttCred']).'</td>';
	 $table .= '<td>CRED.CPT = '._h($v['termCompCred']).'</td>';
	 $table .= '<td style="width:.13em;">GRD.PTS = '._h($v['Points']).'</td>';
	 $table .= '<td>GPA = '.floor((_h($v['termGPA'])*100))/100 . '</td>';
	 $table .= '</tr>';*/
}

$table .= '<tr>';
$table .= '<td colspan="3"><b>'._t( 'Cum. Totals' ).'</b></td>';
$table .= '<td>CRED.ATT = '._h($tranGPA[0]['Attempted']).'</td>';
$table .= '<td>CRED.CPT = '._h($tranGPA[0]['Completed']).'</td>';
$table .= '<td style="width:.13em;">GRD.PTS = '._h($tranGPA[0]['Points']).'</td>';
$table .= '<td>GPA = '.floor((_h($tranGPA[0]['GPA'])*100))/100 . '</td>';
$table .= '</tr>';
 
$table .= '</tbody>';
$table .= '</table>';

$pdf->writeHTML($table, true, 0);

$footer = "<p>*********************************************************************************************************************************************************************************************</p>";
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
if(_h($stuInfo[0]['graduationDate']) != '0000-00-00') {
$footer .= '<td>'._h($stuInfo[0]['degreeCode']).' - ' . _h($stuInfo[0]['degreeName']) . ' Awarded on ' . _h($stuInfo[0]['graduationDate']) . '</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_h($stuInfo[0]['majorCode']) != 'NULL') {
$footer .= '<td>'._h($stuInfo[0]['majorCode']).' - '._h($stuInfo[0]['majorName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_h($stuInfo[0]['minorCode']) != 'NULL') {
$footer .= '<td>'._h($stuInfo[0]['minorCode']).' - '._h($stuInfo[0]['minorName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_h($stuInfo[0]['specCode']) != 'NULL') {
$footer .= '<td>'._h($stuInfo[0]['specCode']).' - '._h($stuInfo[0]['specName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

if(_h($stuInfo[0]['ccdCode']) != 'NULL') {
$footer .= '<td>'._h($stuInfo[0]['ccdCode']).' - '._h($stuInfo[0]['ccdName']).'</td>';
} else {
    $footer .= '<td>&nbsp;</td>';
}

$footer .= '</tr>';
$footer .= '</tbody>';
$footer .= '</table>';
$footer .= "<p>*********************************************************************************************************************************************************************************************</p>";
$footer .= "<p>*"._t( 'Transfer Credits' )."</p>";

$pdf->writeHTML($footer, true, 0);

$txt3 = 'Printed on ' . date("m/d/Y @ h:i A");    

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