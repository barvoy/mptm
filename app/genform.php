<?php
error_reporting(-1);

// Include the main TCPDF library (search for installation path).
require_once('TCPDF-6.2.17/tcpdf.php');
require_once('lib.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("");
$pdf->SetAuthor('Menlo Park Toastmasters');
$pdf->SetTitle('Menlo Park Toastmasters, receipt');
$pdf->SetSubject('Menlo Park Toastmasters, receipt');
$pdf->SetKeywords('Menlo Park, Toastmasters, receipt, bill');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

$expl = <<<EOD
Menlo Park Toastmasters is a chapter of a bigger non-profit organization
called Toastmasters International (TMI). TMI provides us educational
materials and a structured program for out meetings, while Menlo Park
Toastmasters conducts the meetings and sticks to TMI protocol.



EOD;

//

// set font
$pdf->SetFont('helvetica', 'B', 20);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Welcome to Menlo Park Toastmasters!', '', 0, 'L', true, 0, false, false, 0);


$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, $expl, '', 0, 'L', true, 0, false, false, 0);

// ---------

$cfg = get_config();
$order = get_order();
$order_items = make_order_items_array($cfg, $order);

$tbl = make_full_table();

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->Output('example_048.pdf', 'I');
