<?php
	$chars = $list->characters()
                  ->orderBy('online', 'DESC')
                  ->orderBy('level', 'DESC')
                  ->orderBy('name')
                  ->get();
?>

<div class="jumbotron subhead straight">
	<div class="container">
		<h1>{{ $list->title }}</h1>
		<p>{{ $list->description }}</p>
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
		<div class="span12">
			@if ($list->hasBrandNewCharacter())
				<div class="alert alert-info">
					<button type="button" class="close fade in" data-dismiss="alert">×</button>
					<strong>Hey!</strong>
					Some characters in this list were added recently and will not be displayed until updated. This may take
					up to a minute.
				</div>
			@endif
			<table id="chars" class="table table-striped table-hover">
				<thead>
					<tr>
						<th style="width: 25%;">Name</th>
						<th style="min-width: 42px;">Level</th>
						<th style="min-width: 61px;">Vocation</th>
						<th style="width: 25%;">Guild</th>
						<th style="width: 50%;">Notes</th>
						@if ($edit)
							<th>Edit</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@if ($edit)
						<tr class="add-row">
							<td colspan="6">
								<i class="icon-plus"></i>
								<p href="#" class="slide-text">New Character</p>
							</td>
						</tr>
					@endif
					@if (count($chars))
						@foreach ($chars as $char)
							@include('partials.listCharacter', compact('char', 'edit'))
						@endforeach
					@endif
				</tbody>
			</table>


			@if (count($chars) === 0)
				<p class="text-center">
					@if (!$edit)
						{<a href="{{ URL::route('editList', array($list->hash)) }}">
					@endif
							This list seems to be empty! Why don't you add a few characters to it?
					@if (!$edit)
						</a>
					@endif
				</p>
			@endif
		</div>
	</div>
</div>