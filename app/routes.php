<?php

/*
|--------------------------------------------------------------------------
| Application Bindings
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes parameters bindings for
| an application. It's a breeze. Simply tell Laravel the parameters names
| and give it a Closure to execute on that parameter.
|
*/

Route::bind('list', function($hash) {
	$list = CharacterList::find(CharacterList::toId($hash));
	if ($list === null) {
		return App::abort(404);
	} else {
		return $list;
	}
});



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('test/{w?}', array(
	'as' => 'test',
	'do' => function($w='') {
		return (string) is_null(false ? true : false);
	}
));

// Home route
Route::get('/', array(
	'as'	=> 'home',
	'uses'	=> 'MainController@index'
));

// I should probably be worried about these as one of my lists' hash might match
// this route one day, but I'm not; I really don't plan on ever getting to list
// no. 885,268,961; really, that's just too much. If, one day, that happens,
// then I'll worry about it.
Route::get('/create', array(
	'as'	=>	'showCreateList',
	'uses'	=>	'ListController@showCreateList'
));

Route::post('/create', array(
	'as'	=>	'createList',
	'uses'	=>	'ListController@createList'
));

Route::get('/update', array(
	'as'	=>	'updateChars',
	'uses'	=>	'CharacterController@updateCharacters'
));

// List routes
Route::get('/{list}/edit', array(
	'as'	=>	'editList',
	'uses'	=>	'ListController@editList'
))->where('list', '[A-z]+');

Route::post('/{list}/add', array(
	'as'	=>	'addCharToList',
	'uses'	=>	'ListController@addChar'
))->where('list', '[A-z]+');

Route::post('/{list}/remove', array(
	'as'	=>	'removeCharFromList',
	'uses'	=>	'ListController@removeChar'
))->where('list', '[A-z]+');

Route::get('/{list}', array(
	'as'	=>	'showList',
	'uses'	=>	'ListController@showList'
))->where('list', '[A-z]+');

Route::post('/{list}', array(
	'as'	=>	'authShowList',
	'uses'	=>	'ListController@authShowList'
))->where('list', '[A-z]+');

Route::post('/{list}/edit', array(
	'as'	=>	'authEditList',
	'uses'	=>	'ListController@authEditList'
))->where('list', '[A-z]+');