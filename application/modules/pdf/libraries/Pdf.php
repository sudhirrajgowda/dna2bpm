<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter PDF Library
 *
 * Generate PDF's in your CodeIgniter applications.
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Chris Harvey
 * @license			MIT License
 * @link			https://github.com/chrisnharvey/CodeIgniter-PDF-Generator-Library
 */
include(APPPATH.'modules/pdf/vendor/autoload.php') ;
use Dompdf\Dompdf;
// require_once(APPPATH. 'modules/pdf/libraries/dompdf/dompdf.php');
// include(dirname(__FILE__) . '/dompdf/dompdf_config.inc.php');



//spl_autoload_register('DOMPDF_autoload');

class Pdf extends DOMPDF
{
	 public function __construct(){
		parent::__construct();
		// $this->set_option('debugKeepTemp', TRUE);
		// $this->set_option('chroot', '/'); // Just for testing :)
		
	 	$this->set_option('isRemoteEnabled', TRUE);
		$this->set_option('isHtml5ParserEnabled', true);
		$this->set_option('defaultPaperSize', 'A4');
		
		// //-----4debug
		// $this->set_option('debugLayoutBlocks', true);
		// $this->set_option('debugLayoutLines', true);
		// $this->set_option('debugLayoutInline', true);
		// $this->set_option('debugLayoutPaddingBox', true);
		// $this->set_option('debugCss', true);
	 	    
	 	
	 }
	 
	 /**
	 * Get an instance of CodeIgniter
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function ci()
	{
		return get_instance();
	}

	/**
	 * Load a CodeIgniter view into domPDF
	 *
	 * @access	public
	 * @param	string	$view The view to load
	 * @param	array	$data The view data
	 * @return	void
	 */
	public function load_view($view, $data = array())
	{
		$html = $this->ci()->load->view($view, $data, TRUE);

		$this->load_html($html);
	}
	public function parse($view, $data = array())
	{
		$html = $this->ci()->parser->parse($view, $data, TRUE);

		$this->load_html($html);
	}
}