<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/*
Template Name: Default
Template Slug: default
*/

/**
 * Default Student Roster View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.0.9
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/blank');
$app->view->block('blank');

// create new PDF document
$pdf = new \app\src\tcpdf\Tcpdf('portrait', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);

// set default header data
$pdf->SetHeaderData('', '', 'Section Roster', '', '', '');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, "20", PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin("12");
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set pdf page title
$pdf->SetTitle($sros[0]['courseSection']);

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', '', 10);

// add a page
$pdf->AddPage();

// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

// set some text for student info
$txt1 = $app->hook->{'get_option'}('institution_name')."<br />";
$txt1 .= "Section: "._h($sros[0]['courseSection'])." "._h($sros[0]['secShortTitle'])."<br />";
$txt1 .= "Instructor: ".get_name(_h($sros[0]['facID']))."<br />";

// writeHTMLCell
$pdf->writeHTMLCell(0, 0, '', '', $txt1, 0, 1, 0, true, 'L', true);

$schedule = '- - - - - - - - - - - - - - - - - - - - - - - - - - Schedule - - - - - - - - - - - - - - - - - - - - - - - - - -<br />';
$schedule .= _h($sros[0]['startDate']) .' '. _h($sros[0]['endDate']) .'&nbsp;&nbsp;&nbsp;&nbsp;'. _h($sros[0]['roomCode']) .'&nbsp;&nbsp;&nbsp;&nbsp;'. _h($sros[0]['instructorMethod']) .'&nbsp;&nbsp;&nbsp;&nbsp;'. _h($sros[0]['dotw']) .'&nbsp;&nbsp;&nbsp;&nbsp;'. _h($sros[0]['startTime']) .' ' . _h($sros[0]['endTime']);

 // print a block of text using Write()
$pdf->writeHTMLCell(0, 0, '', '', $schedule, 0, 1, 0, true, 'C', true);

// column titles
$table = '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped" id="table-example">';
$table .= '<thead><tr>';
$table .= '<th><b>'._t( 'ID' ).'</b></th>';
$table .= '<th><b>'._t( 'Name' ).'</b></th>';
$table .= '<th><b>'._t( 'Acad Level' ).'</b></th>';
$table .= '<th><b>'._t( 'Acad Program' ).'</b></th>';
$table .= '<th><b>'._t( 'Acad Credit Status' ).'</b></th>';
$table .= '</tr></thead>';
$table .= '<tbody>';
foreach($sros as $k => $v) {
     $table .= '<tr>';
     $table .= '<td>'._h($v['stuID']).'</td>';
     $table .= '<td>'.get_name(_h($v['stuID'])).'</td>';
     $table .= '<td>'._h($v['acadLevelCode']).'</td>';
     $table .= '<td>'._h($v['acadProgCode']).'</td>';
     $table .= '<td>'._h($v['Status']).'</td>';
     $table .= '</tr>';
}
 
$table .= '</tbody>';
$table .= '</table>';

$pdf->writeHTML($table, true, 0);

$students = '<p>'._h($count[0]['count']).' students currently enrolled.</p>';
$students .= '<p>&nbsp;</p>';

$pdf->writeHTML($students, true, 0);

$txt3 = 'Printed on ' . date("m/d/Y @ h:i A");    

 // print a block of text using Write()
$pdf->Write($h=0, $txt3, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

// close and output PDF document
$pdf->Output($sros[0]['courseSection'], 'I');

//============================================================+
// END OF FILE
//============================================================+
$app->view->stop();