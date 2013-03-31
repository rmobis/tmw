<div class="jumbotron subhead straight">
	<div class="container">
		<h1>Ooops!</h1>
		<p>
			@if (!$edit)
				This is a private list! To access it, please enter the list\'s password below.
			@else
				You're trying to edit a list! To edit it, please enter the list's edit password below.
			@endif
		</p>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="span6 offset3">
			<div class="alert-wrapper">
				@if (count($errors))
					<div class="alert alert-error">
						<button type="button" class="close fade in" data-dismiss="alert">&times;</button>
						<strong>Oh snap!</strong>
						{{{ $errors->first('password', ' :message') }}}
					</div>
				@endif
			</div>
				{{ Former::vertical_open(URL::route($edit ? 'authEditList' : 'authShowList', array($list->hash))) }}
				<?php echo(
					  Former::password('password')
					  		->label('')
					  		->class('input-huge')
					  		->placeholder('Password')
					  		->autofocus()
					  .
					  Former::submit('Submit')
					  		->class('btn btn-primary btn-huge')
				);?>
			{{ Former::close() }}
		</div>
	</div>
</div>