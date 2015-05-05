<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * PDF Catalog View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.0.1
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
$pdf->SetTitle($catalog[0]['termCode'].' Course Catalog');

// set default header data
$pdf->SetHeaderData("", "", $catalog[0]['termCode'].' Course Catalog', "");

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

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 8);

// column titles
$table = '<table cellpadding="2" cellspacing="2" border="0" class="table table-striped" id="table-example">';
$table .= '<thead><tr>';
$table .= '<th><b>'._t( 'Course Section' ).'</b></th>';
$table .= '<th style="width:125px;"><b>'._t( 'Title' ).'</b></th>';
$table .= '<th><b>'._t( 'Instructor' ).'</b></th>';
$table .= '<th><b>'._t( 'Credits' ).'</b></th>';
$table .= '<th><b>'._t( 'Days' ).'</b></th>';
$table .= '<th><b>'._t( 'Time' ).'</b></th>';
$table .= '<th><b>'._t( 'Location' ).'</b></th>';
$table .= '<th><b>'._t( 'Building' ).'</b></th>';
$table .= '<th><b>'._t( 'Room' ).'</b></th>';
$table .= '</tr></thead>';
$table .= '<tbody>';
foreach($catalog as $k => $v) {
     $table .= '<tr>';
     $table .= '<td>'._h($v['courseSecCode']).'</td>';
     $table .= '<td style="width:125px;">'._h($v['secShortTitle']).'</td>';
     $table .= '<td>'.get_initials(_h($v['facID']),1).'</td>';
	 $table .= '<td>'._h($v['minCredit']).'</td>';
     $table .= '<td>'._h($v['dotw']).'</td>';
     $table .= '<td>'._h($v['startTime']).' &nbsp;&nbsp; '._h($v['endTime']).'</td>';
	 $table .= '<td>'._h($v['locationCode']).'</td>';
	 $table .= '<td>'._h($v['buildingCode']).'</td>';
     $table .= '<td>'._h($v['roomCode']).'</td>';
     $table .= '</tr>';
}
$table .= '</tbody>';
$table .= '</table>';

$pdf->AddPage();
$pdf->writeHTML($table, true, 0);

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('catalog-'.$catalog[0]['termCode'], 'I');

//============================================================+
// END OF FILE
//============================================================+
$app->view->stop();