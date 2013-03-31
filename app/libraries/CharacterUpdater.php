<?php

use \Underscore\Methods\ObjectMethods;
use \Symfony\Component\DomCrawler\Crawler;

/**
 * @needdocs
 */
class CharacterUpdater {

	/**
	 * URL to use when searching for characters info on Tibia.com
	 *
	 * @var string
	 */
	const CHAR_SEARCH_URL = 'http://www.tibia.com/community/?subtopic=characters&name=';

	/**
	 * URL to use when searching for worlds info on Tibia.com
	 *
	 * @var string
	 */
	const WORLD_SEARCH_URL = 'http://www.tibia.com/community/?subtopic=worlds&world=';

	/**
	 * List of all worlds existing in Tibia.
	 *
	 * @var array
	 */
	public static $worlds = array(
		'Aldora',	'Amera',	'Antica',	'Arcania',	'Askara',	'Astera',	'Aurea',	'Aurera',	'Aurora',
		'Azura',	'Balera',	'Berylia',	'Calmera',	'Candia',	'Celesta',	'Chimera',	'Danera',	'Danubia',
		'Dolera',	'Elera',	'Elysia',	'Empera',	'Eternia',	'Fidera',	'Fortera',	'Furora',	'Galana',
		'Grimera',	'Guardia',	'Harmonia',	'Hiberna',	'Honera',	'Inferna',	'Iridia',	'Isara',	'Jamera',
		'Julera',	'Keltera',	'Kyra',		'Libera',	'Lucera',	'Luminera',	'Lunara',	'Magera',	'Malvera',
		'Menera',	'Morgana',	'Mythera',	'Nebula',	'Neptera',	'Nerana',	'Nova',		'Obsidia',	'Ocera',
		'Olympa',	'Pacera',	'Pandoria',	'Premia',	'Pythera',	'Refugia',	'Rubera',	'Samera',	'Saphira',
		'Secura',	'Selena',	'Shanera',	'Shivera',	'Silvera',	'Solera',	'Tenebra',	'Thoria',	'Titania',
		'Trimera',	'Unitera',	'Valoria',	'Vinera',	'Xantera',	'Xerena',	'Zanera'
	);



	/**
	 * Get the URL on Tibia.com that links to the character with given name.
	 *
	 * @param $name Character's name
	 * @return string
	 */
	public static function getCharUrl($name) {
		return self::CHAR_SEARCH_URL.str_replace(' ', '+', $name);
	}

	/**
	 * Get the URL on Tibia.com that links to the world with given name.
	 *
	 * @param $name World's name
	 * @return string
	 */
	public static function getWorldUrl($name) {
		return self::WORLD_SEARCH_URL.$name;
	}



	/**
	 *
	 */
	public static function updateOnline() {
		// Increase the maximum execution time so we don't get stopped when
		// updating a crapload of chars
		set_time_limit(300);

		$data = ObjectMethods::group(
			Character::get(array('id', 'name', 'world'))->all(),
			function ($o) {
				return $o->world;
			}
		);

		// TODO: Redo this using curl_multi_init or Guzzle
		foreach ($data as $world => $chars) {
			static::updateOnlineOnWorld($world, $chars);
		}
	}



	public static function updateOnlineOnWorld($world, $chars) {
		$nodes = static::getWorldData($world);
		$parsedChars = static::parseWorldData($nodes);

		foreach ($chars as $char) {
			if (array_key_exists($char->name, $parsedChars)) {
				Profiler::logInfo('Found '.$char->name);
				$parsedData = $parsedChars[$char->name];
				$char->level = $parsedData['level'];
				$char->vocation = $parsedData['vocation'];
				$char->online = true;

				$char->save();
			}
		}
	}

	private static function parseWorldData($nodes) {
		$chars = array();

		foreach ($nodes as $node) {
			$char = array();
			$children = (new Crawler($node))->children();

			// We use the last <a> tag because some items may have more than
			// one, as they're used by Tibia website to link to the characters
			// starting with specific letter.
			$name = $children->filter('a:last-child')->text();
			$level = intval($children->eq(1)->text());
			$voc = $children->eq(2)->text();

			// We gotta normalize the strings, as they might (they do) come in
			// different encodings and that will fuck up when comparing to our
			// own strings. We also trim our data, because Tibia developers are
			// fucking assholes and decided adding a whitespace after some data
			// is a nice way to make other developers lose fucking hours of work
			$name = static::normalizeString($name);
			$voc = static::normalizeString($voc);

			$chars[$name] = array(
				'level' => $level,
				'vocation' => $voc
			);
		}

		return $chars;
	}

	private static function getWorldData($world) {
		// Get the whole page data and parse it with the DOMCrawler
		$crawler = new Crawler(static::getWorldPage($world));

		// Get only the <tr> elements, each representing one online char; we
		// skip the first because it's the heading of the table
		$crawler = $crawler->filter('.Table2 table tr:not(:first-child)');

		return $crawler;
	}

	public static function getWorldPage($world) {
		$ch = curl_init(static::getWorldUrl($world));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}






	public static function updateNewCharacters() {
		// Increase the maximum execution time so we don't get stopped when
		// updating a crapload of chars
		set_time_limit(300);

		$chars =   Character::where('created_at', '=', DB::raw('updated_at'))
							->get(array('id', 'name'));

		foreach ($chars as $char) {
			static::updateOfflineCharacter($char);
		}
	}

	public static function updateNoLongerOnline() {
		// Increase the maximum execution time so we don't get stopped when
		// updating a crapload of chars
		set_time_limit(300);

		$chars =   Character::outOfDate()
							->where('online', '=', true)
							->get(array('id', 'name'));

		foreach ($chars as $char) {
			static::updateOfflineCharacter($char);
		}
	}

	private static function updateOfflineCharacter($char) {
		$data = static::getCharData($char->name);
		$parsedData = static::parseCharData($data);

		if ($parsedData === null) {
			$char->lists()->delete();
			$char->delete();
			Log::info('Character ' . $char->name . ' was deleted because it doesn\'t exist.');

			return;
		} else {
			$updatable = array('name', 'level', 'vocation', 'guild', 'world');
			foreach ($updatable as $updating) {
				if (array_key_exists($updating, $parsedData)) {
					$char->$updating = $parsedData[$updating];
				}
			}
		}
		$char->online = false;

		$char->save();
	}

	private static function parseCharData($rows) {
		$char = array();

		foreach ($rows as $row) {
			$children = (new Crawler($row))->children();
			$label = static::normalizeString(trim($children->first()->text(), ':'));
			$valueNode = $children->last();

			if (preg_match('/^Character [A-z ]{2,32} does not exist\.$/', $valueNode->text())) {
				return null;
			}

			switch ($label) {
				case 'Name':
					$char['name'] = $valueNode->text();
					break;

				case 'Level':
					$char['level'] = intval($valueNode->text());
					break;

				case 'Vocation':
					$char['vocation'] = $valueNode->text();
					break;

				case 'Guild membership':
					$char['guild'] = $valueNode->filter('a')->text();
					break;

				case 'World':
					$char['world'] = $valueNode->text();
					break;

				default:
					Log::info('Unhandled label \''.$label.'\'');
					break;
			}
		}

		// We gotta normalize the strings, as they might (they do) come in
		// different encodings and that will fuck up when comparing to our
		// own strings.
		return static::normalizeString($char);
	}

	private static function getCharData($char) {
		// Get the whole page data and parse it with the DOMCrawler
		$crawler = new Crawler(static::getCharPage($char));

		// Get only the <tr> elements holding the characters information; we
		// skip the first because it's the heading of the table
		$crawler = $crawler->filter('.BoxContent > table:first-child tr:not(:first-child)');

		return $crawler;
	}

	private static function getCharPage($char) {
		$ch = curl_init(static::getCharUrl($char));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}

	private static function normalizeString($value) {
		if (is_string($value)) {
			return trim(Str::ascii($value));
		} else if (is_array($value)) {
			return array_map('static::normalizeString', $value);
		} else {
			return $value;
		}
	}

}