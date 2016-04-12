<?php
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Template;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

// autoloader
require_once 'vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';


/**
 * This class generates the dynamic terms document for e-mails
 *
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
class TermsGenerator {

	const PDF_RENDERER  = 'admin/tcpdf';
	const TEMPLATE_FILE = 'pdf/templates/template-v4.docx';
	const TMP_LOCATION  = 'tmp';
	const TERMS_VERSION = '31/03/2016';

	private static $autoloader  = true;
	private static $pdfRenderer = 'admin/tcpdf';

	public static function generate($website) {

		if (true === self::$autoloader) {

			self::$autoloader = false;
			Autoloader::register();
		}

		$root	 	= dirname(dirname(dirname(__FILE__))) . '/';
		$tmpFile 	= $root . self::TMP_LOCATION . '/tmp-' . time() . '.docx';
		$resultFile = $root . self::TMP_LOCATION . '/result-' . time() . '.pdf';

		$doc = new Template($root . self::TEMPLATE_FILE);
		$doc->setValue('name', $website);
		$doc->setValue('version', self::TERMS_VERSION);
		$doc->setValue('bullet', html_entity_decode('&bull;'));
		$doc->saveAs($tmpFile);

		Settings::setPdfRendererPath($root . self::PDF_RENDERER);
		Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);

		$file = IOFactory::load($tmpFile);
		$pdf  = IOFactory::createWriter($file, 'PDF');
		$pdf->save($resultFile, true);

		unlink($tmpFile);
		return $resultFile;
	}
}