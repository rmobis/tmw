<div class="jumbotron subhead straight">
	<div class="container">
		<h1>Create a list</h1>
		<p></p>
	</div>
</div>
<div class="container">
	<div id="error-modal" class="modal hide fade" tabindex="-1">
		<div class="modal-header">
			<button class="close" data-dismiss="modal">x</button>
			<h3>Error!</h3>
		</div>
		<div class="modal-body">
			<p class="modal-message"></p>
		</div>
	</div>
	<div class="row">
		<div class="span6 offset3">
			<?php echo(
				Former::open()
				      ->action(URL::route('createList'))
				      ->method('POST')
				      ->style('margin-top: 35px;')
				      ->rules($createListRules) .


					Former::xlarge_text('list_name')
					      ->label('List Name') .

					Former::xlarge_text('description')
					      ->label('Description')
					      ->placeholder('Optional') .

					Former::xlarge_password('master_password')
					      ->label('Master Password')
					      ->append('<a ' .
						      Html::attributes(
							      array(
								      'href'         => 'javascript:void(0);',
								      'class'        => 'form-popover',
								      'title'        => 'Master Password',
								      'tabindex'     => '-1',
								      'data-toggle'  => 'popover',
								      'data-trigger' => 'hover',
								      'data-content' => 'This gives you full power over the list. That includes viewing, editing and adding/removing characters.'
							      )
						      ) .
						      '><i class="icon-info-sign"></i></a>'
					      ) .

					Former::xlarge_password('master_password_confirmation')
					      ->label('Confirm Master Pass.') .

					Former::xlarge_password('public_password')
					      ->label('Public Password')
					      ->placeholder('Optional')
					      ->append('<a ' .
						      Html::attributes(
							      array(
								      'href'         => 'javascript:void(0);',
								      'class'        => 'form-popover',
								      'title'        => 'Public Password',
								      'tabindex'     => '-1',
								      'data-toggle'  => 'popover',
								      'data-trigger' => 'hover',
								      'data-content' => 'If this is left blank, the list will be considered public and everyone will be allowed to see its contents.'
							      )
						      ) .
						      '><i class="icon-info-sign"></i></a>'
					      ) .

					Former::xlarge_password('public_password_confirmation')
					      ->label('Confirm Public Pass.')
					      ->placeholder('Optional') .

					'<div class="control-group"><div class="controls">' .
						Former::success_submit('Create List') .
					'</div></div>' .

				Former::close()
				);
			?>
		</div>
	</div>
</div>
<script>
	$('.form-popover').popover({container: 'body'});
</script>]