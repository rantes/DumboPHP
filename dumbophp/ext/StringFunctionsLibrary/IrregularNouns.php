<?php
/**
 * Definiciones de las palabras irregulares para singular y plural.
 *
 * @author Javier Serrano
 * @package Core
 * @subpackage Extensions
 * @Version 3.0 November 18 2009
 */
/**
 * Gestiona las palabras irregulares para convertir singular/plural
 * @package Core
 * @subpackage Extensions
 * @author Javier Serrano
 */
class IrregularNouns {
	/**
	 * Contiene todas las palabras en singular.
	 * @var array $singular
	 */
	public $singular = array();
	/**
	 * Contiene las palabras en plural.
	 * @var array $plural
	 */
	public $plural = array();
	/**
	 * Establece los arreglos de palabras en singular y plural.
	 */
	function __construct(){
		$this->singular[] =	'abyss';
		$this->singular[] =	'alumnus';
		$this->singular[] =	'analysis';
		$this->singular[] =	'aquarium';
		$this->singular[] =	'arch';
		$this->singular[] =	'atlas';
		$this->singular[] =	'axe';
		$this->singular[] =	'baby';
		$this->singular[] =	'bacterium';
		$this->singular[] =	'batch';
		$this->singular[] =	'beach';
		$this->singular[] =	'browse';
		$this->singular[] =	'brush';
		$this->singular[] =	'bus';
		$this->singular[] =	'calf';
		$this->singular[] =	'chateau';
		$this->singular[] =	'cherry';
		$this->singular[] =	'child';
		$this->singular[] =	'church';
		$this->singular[] =	'circus';
		$this->singular[] =	'city';
		$this->singular[] =	'cod';
		$this->singular[] =	'copy';
		$this->singular[] =	'crisis';
		$this->singular[] =	'curriculum';
		$this->singular[] =	'datum';
		$this->singular[] =	'deer';
		$this->singular[] =	'dictionary';
		$this->singular[] =	'diagnosis';
		$this->singular[] =	'domino';
		$this->singular[] =	'dwarf';
		$this->singular[] =	'echo';
		$this->singular[] =	'elf';
		$this->singular[] =	'emphasis';
		$this->singular[] =	'family';
		$this->singular[] =	'fax';
		$this->singular[] =	'fish';
		$this->singular[] =	'flush';
		$this->singular[] =	'fly';
		$this->singular[] =	'foot';
		$this->singular[] =	'fungus';
		$this->singular[] =	'half';
		$this->singular[] =	'hero';
		$this->singular[] =	'hippopotamus';
		$this->singular[] =	'hoax';
		$this->singular[] =	'hoof';
		$this->singular[] =	'index';
		$this->singular[] =	'iris';
		$this->singular[] =	'kiss';
		$this->singular[] =	'knife';
		$this->singular[] =	'lady';
		$this->singular[] =	'leaf';
		$this->singular[] =	'life';
		$this->singular[] =	'loaf';
		$this->singular[] =	'man';
		$this->singular[] =	'mango';
		$this->singular[] =	'memorandum';
		$this->singular[] =	'mess';
		$this->singular[] =	'moose';
		$this->singular[] =	'motto';
		$this->singular[] =	'mouse';
		$this->singular[] =	'nanny';
		$this->singular[] =	'neurosis';
		$this->singular[] =	'nucleus';
		$this->singular[] =	'oasis';
		$this->singular[] =	'octopus';
		$this->singular[] =	'page';
		$this->singular[] =	'party';
		$this->singular[] =	'pass';
		$this->singular[] =	'penny';
		$this->singular[] =	'person';
		$this->singular[] =	'plateau';
		$this->singular[] =	'poppy';
		$this->singular[] =	'potato';
		$this->singular[] =	'purchase';
		$this->singular[] =	'quiz';
		$this->singular[] =	'reflex';
		$this->singular[] =	'runner-up';
		$this->singular[] =	'scarf';
		$this->singular[] =	'scratch';
		$this->singular[] =	'series';
		$this->singular[] =	'sheaf';
		$this->singular[] =	'sheep';
		$this->singular[] =	'shelf';
		$this->singular[] =	'son-in-law';
		$this->singular[] =	'species';
		$this->singular[] =	'splash';
		$this->singular[] =	'spy';
		$this->singular[] =	'status';
		$this->singular[] =	'stitch';
		$this->singular[] =	'story';
		$this->singular[] =	'syllabus';
		$this->singular[] =	'tax';
		$this->singular[] =	'thesis';
		$this->singular[] =	'thief';
		$this->singular[] =	'tomato';
		$this->singular[] =	'tooth';
		$this->singular[] =	'tornado';
		$this->singular[] =	'try';
		$this->singular[] =	'volcano';
		$this->singular[] =	'waltz';
		$this->singular[] =	'wash';
		$this->singular[] =	'watch';
		$this->singular[] =	'wharf';
		$this->singular[] =	'wife';
		$this->singular[] =	'woman';
		
		$this->plural[] =	'abysses';
		$this->plural[] =	'alumni';
		$this->plural[] =	'analyses';
		$this->plural[] =	'aquaria';
		$this->plural[] =	'arches';
		$this->plural[] =	'atlases';
		$this->plural[] =	'axes';
		$this->plural[] =	'babies';
		$this->plural[] =	'bacteria';
		$this->plural[] =	'batches';
		$this->plural[] =	'beaches';
		$this->plural[] =	'browses';
		$this->plural[] =	'brushes';
		$this->plural[] =	'buses';
		$this->plural[] =	'calves';
		$this->plural[] =	'chateaux';
		$this->plural[] =	'cherries';
		$this->plural[] =	'children';
		$this->plural[] =	'churches';
		$this->plural[] =	'circuses';
		$this->plural[] =	'cities';
		$this->plural[] =	'cod';
		$this->plural[] =	'copies';
		$this->plural[] =	'crises';
		$this->plural[] =	'curricula';
		$this->plural[] =	'data';
		$this->plural[] =	'deer';
		$this->plural[] =	'dictionaries';
		$this->plural[] =	'diagnoses';
		$this->plural[] =	'dominoes';
		$this->plural[] =	'dwarves';
		$this->plural[] =	'echoes';
		$this->plural[] =	'elves';
		$this->plural[] =	'emphases';
		$this->plural[] =	'families';
		$this->plural[] =	'faxes';
		$this->plural[] =	'fish';
		$this->plural[] =	'flushes';
		$this->plural[] =	'flies';
		$this->plural[] =	'feet';
		$this->plural[] =	'fungi';
		$this->plural[] =	'halves';
		$this->plural[] =	'heroes';
		$this->plural[] =	'hippopotami';
		$this->plural[] =	'hoaxes';
		$this->plural[] =	'hooves';
		$this->plural[] =	'indexes';
		$this->plural[] =	'irises';
		$this->plural[] =	'kisses';
		$this->plural[] =	'knives';
		$this->plural[] =	'ladies';
		$this->plural[] =	'leaves';
		$this->plural[] =	'lives';
		$this->plural[] =	'loaves';
		$this->plural[] =	'men';
		$this->plural[] =	'mangoes';
		$this->plural[] =	'memoranda';
		$this->plural[] =	'messes';
		$this->plural[] =	'moose';
		$this->plural[] =	'mottoes';
		$this->plural[] =	'mice';
		$this->plural[] =	'nannies';
		$this->plural[] =	'neuroses';
		$this->plural[] =	'nuclei';
		$this->plural[] =	'oases';
		$this->plural[] =	'octopi';
		$this->plural[] =	'pages';
		$this->plural[] =	'parties';
		$this->plural[] =	'passes';
		$this->plural[] =	'pennies';
		$this->plural[] =	'people';
		$this->plural[] =	'plateaux';
		$this->plural[] =	'poppies';
		$this->plural[] =	'potatoes';
		$this->plural[] =	'shopping';
		$this->plural[] =	'quizzes';
		$this->plural[] =	'reflexes';
		$this->plural[] =	'runners-up';
		$this->plural[] =	'scarves';
		$this->plural[] =	'scratches';
		$this->plural[] =	'series';
		$this->plural[] =	'sheaves';
		$this->plural[] =	'sheep';
		$this->plural[] =	'shelves';
		$this->plural[] =	'sons-in-law';
		$this->plural[] =	'species';
		$this->plural[] =	'splashes';
		$this->plural[] =	'spies';
		$this->plural[] =	'statuses';
		$this->plural[] =	'stitches';
		$this->plural[] =	'stories';
		$this->plural[] =	'syllabi';
		$this->plural[] =	'taxes';
		$this->plural[] =	'theses';
		$this->plural[] =	'thieves';
		$this->plural[] =	'tomatoes';
		$this->plural[] =	'teeth';
		$this->plural[] =	'tornadoes';
		$this->plural[] =	'tries';
		$this->plural[] =	'volcanoes';
		$this->plural[] =	'waltzes';
		$this->plural[] =	'washes';
		$this->plural[] =	'watches';
		$this->plural[] =	'wharves';
		$this->plural[] =	'wives';
		$this->plural[] =	'women';
	}
}
?>