<?php

class Waldviertlerhof extends FoodGetterVenue {

	function __construct() {
		$this->title = 'Waldviertlerhof';
		//$this->title_notifier = 'BETA';
		$this->address = 'Schönbrunnerstrasse 20, 1050 Wien';
		$this->addressLat = '48.193692';
		$this->addressLng = '16.358687';
		$this->url = 'http://www.waldviertlerhof.at/';
		$this->dataSource = 'http://www.waldviertlerhof.at/assets/w4h_mittagsmenue.pdf';
		$this->menu = 'http://www.waldviertlerhof.at/assets/w4h_speisen_getränke2.pdf';
		$this->statisticsKeyword = 'waldviertlerhof';
		$this->no_menu_days = array(0, 6);
		$this->lookaheadSafe = true;
		$this->price_nested_info = 'Menü / Tagesteller / Fischmenü Freitag';

		parent::__construct();
	}

	protected function get_today_variants() {
		$today_variants[] = getGermanDayName();
		return $today_variants;
	}

	protected function parseDataSource() {
		//$dataTmp = pdftotxt_ocr($this->dataSource);
		$dataTmp = pdftotext($this->dataSource);
		if (stripos($dataTmp, 'urlaub') !== false)
			return ($this->data = VenueStateSpecial::Urlaub);
		//return error_log($dataTmp);

		// check date range
		if (!$this->in_date_range_string($dataTmp, $this->timestamp)) {
			return;
		}

		// check menu food count
		if ($this->get_holiday_count($dataTmp) + $this->get_soup_count($dataTmp) != 5) {
			return;
		}

		// remove unwanted stuff
		$data = $dataTmp;
		//$data = preg_replace("/([a-z])\n([a-z])/i", '$1 $2', $data);
		// remove multiple newlines
		$data = preg_replace("/(\n)+/i", "\n", $data);
		$data = trim($data);
		//return error_log($data);
		// split per new line
		$foods = explode("\n", $data);
		//return error_log(print_r($foods, true));

		$data = $this->parse_foods_independant_from_days($foods, ', ', $prices, true, false);
		//return error_log($data);
		//return error_log(print_r($prices, true));

		$this->data = $data;
		$this->price = array($prices);

		// set date
		$this->date = reset($this->get_today_variants());

		return $this->data;
	}
}
