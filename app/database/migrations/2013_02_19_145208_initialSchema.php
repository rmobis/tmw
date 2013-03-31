<?php

use Illuminate\Database\Migrations\Migration;

class InitialSchema extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create('character_lists', function($tab) {

			$tab->increments('id');
			$tab->string('title', 128);
			$tab->string('description', 256)
				->nullable();
			$tab->string('show_password', 60)
				->nullable();
			$tab->string('edit_password', 60);
			$tab->timestamp('last_seen_at');
			$tab->timestamps();

		});

		Schema::create('characters', function($tab) {

			$tab->increments('id');
			$tab->string('name', 32)
				->unique();
			$tab->boolean('online');
			$tab->integer('level')
				->unsigned();
			$tab->enum('vocation', array(
				'Knight', 'Paladin', 'Druid', 'Sorcerer',
				'Elite Knight', 'Royal Paladin', 'Elder Druid', 'Master Sorcerer'
			));
			$tab->string('guild', 32)
				->nullable();
			$tab->enum('world', array(
				'Aldora', 'Amera', 'Antica', 'Arcania', 'Askara', 'Astera', 'Aurea', 'Aurera', 'Aurora', 'Azura',
				'Balera', 'Berylia', 'Calmera', 'Candia', 'Celesta', 'Chimera', 'Danera', 'Danubia', 'Dolera', 'Elera',
				'Elysia', 'Empera', 'Eternia', 'Fidera', 'Fortera', 'Furora', 'Galana', 'Grimera', 'Guardia',
				'Harmonia', 'Hiberna', 'Honera', 'Inferna', 'Iridia', 'Isara', 'Jamera', 'Julera', 'Keltera', 'Kyra',
				'Libera', 'Lucera', 'Luminera', 'Lunara', 'Magera', 'Malvera', 'Menera', 'Morgana', 'Mythera', 'Nebula',
				'Neptera', 'Nerana', 'Nova', 'Obsidia', 'Ocera', 'Olympa', 'Pacera', 'Pandoria', 'Premia', 'Pythera',
				'Refugia', 'Rubera', 'Samera', 'Saphira', 'Secura', 'Selena', 'Shanera', 'Shivera', 'Silvera', 'Solera',
				'Tenebra', 'Thoria', 'Titania', 'Trimera', 'Unitera', 'Valoria', 'Vinera', 'Xantera', 'Xerena', 'Zanera'
			));
			$tab->timestamps();

		});

		Schema::create('list_characters', function($tab) {

			$tab->integer('character_list_id')
				->unsigned();
			$tab->integer('character_id')
				->unsigned();
			$tab->string('observation', 256)
				->nullable();

			$tab->primary(array('character_list_id', 'character_id'));

		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

		Schema::drop('character_lists');
		Schema::drop('characters');
		Schema::drop('list_characters');

	}

}