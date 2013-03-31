@if (!$char->isBrandNew())
	<tr class="char"
		data-id="{{{ $char->id }}}"
		data-status="{{{ $char->status }}}"
		data-name="{{{ $char->name }}}"
		data-level="{{{ $char->level }}}"
		data-vocation="{{{ $char->voc }}}"
		data-guild="{{{ $char->guild }}}"
		data-observation="{{{ $char->pivot->observation }}}
	">
		<td>
			<span title="{{{ ucfirst($char->status) }}}" class="status {{{ $char->status }}}"></span>
			<a href="{{{ CharacterUpdater::getCharUrl($char->name) }}}">{{{ $char->name }}}</a>
		</td>
		<td>{{{ $char->level }}}</td>
		<td>{{{ $char->voc }}}</td>
		<td>
			@if (empty($char->guild))
				<span class="muted">-</span>
			@else
				{{{ $char->guild }}}
			@endif</td>
		<td>
			@if (empty($char->pivot->observation))
				<span class="muted">-</span>
			@else
				{{{ $char->pivot->observation }}}
			@endif
		</td>
		@if ($edit)
			<td>
				<a href="{{ URL::route('home') }}"><i class="icon-remove"></i></a>
			</td>
		@endif
	</tr>
@endif