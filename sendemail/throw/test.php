<?php 
	include "../phpspreadsheet/vendor/autoload.php";
	
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$cell_name = "B1";
	$sheet->getStyle( $cell_name )->getFont()->setBold( true );
	$sheet->mergeCells("B1:C1");
	//$sheet->setCellValue('B1','Student being assessed');
	$sheet->setCellValueByColumnAndRow(28, 5, 'PhpSpreadsheet');
	
	$writer = new Xlsx($spreadsheet);
	$writer->save('hello.xlsx');
	
	/*$i = 'a';
	$letterAscii = ord($i);
	for($a=1;$a<=29;$a++)
	{
		$letter = chr($letterAscii);
		echo $letter;
		$letterAscii++;
		
	}*/
	//$columnLetter = $sheet->stringFromColumnIndex(1);
	//echo $columnLetter;
?>