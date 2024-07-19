<?php 
/**
 * Cette classe a pour but de générer les éléments Html dont on a besoin (un titre, un tableau, un formulaire)
 */
class HtmlChoupinator {

    public $html = '';
	// Retour chariot / return line
	private $char_rl;
	public $auto_newline;
	public $auto_div;
	private $indice_name = 0;

    function __construct() {
        $this->char_rl = chr(10);
		$this->auto_newline = false;
        $this->auto_div = true;
    }

	function generate(){
		echo $this->html;
		$this->html = '';
	}

    /**
	* créer un nom unique à partir du name d'un élément Html
	*
	* @param [text] $basename
	* @return $basename incrémenté +1
	*/
	private function get_indice_name($basename) {

		return 'ch_'.$basename."_".($this->indice_name++);

	}

	/**
	* La fonction shortcode_atts de wordpress permettant du fusionner les attributs html sera réécrite ici
	*/
	function shortcode_atts($pairs, $atts, $shortcode = '') {
		$atts = (array) $atts;
		$out = array();
	
		foreach ($pairs as $name => $default) {
			if (array_key_exists($name, $atts)) {
				$out[$name] = $atts[$name];
			} else {
				$out[$name] = $default;
			}
		}
	
		return $out;
	}

    /**
    * Génrérer un titre
    */
    function add_title($atts, $add_flush_html = true){

		$a = $this->shortcode_atts( array(
			'name' => $this->get_indice_name('title'), // création du nom unique
			'title' => 'titre',
			'level' => 2, // h2 par défaut
			'class' => 'wp-heading-inline',
			), $atts );
		 
		$r = '<h'.$a['level'].' class="'.$a['class'].'" id="add_'.$a['name'].'" >'.$a['title'].'</h'.$a['level'].''.' name="'.$a['name'].'">';

		// Retour chariot pour meilleure lecture html
		$r .= $this->char_rl;

		// on concaténe pour le flush
		if ($add_flush_html) $this->html .= $r;
		
		return $r;
	}
}
?>