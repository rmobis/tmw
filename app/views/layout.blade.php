<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!--
		TODO: Get these done
		<link rel="apple-touch-icon" href="/public/assets/img/touch-icon.png">
		<link rel="icon" href="/public/assets/img/favicon.png">
		<meta name="msapplication-TileColor" content="#000000">
		<meta name="msapplication-TileImage" content="/public/assets/img/tile-image.png">
		-->

		<meta name="description" content="Tibia Most Wanted is a sleek and intuitive way to create your own list of Tibia characters.">
		<meta name="keywords" content="tibia, war, fight, tnuc, tnuc.org, list, charlist, char, hunted, hunt, enemy, enemies">
		<meta name="robots" content="index,follow">
		<meta name="application-name" content="Tibia Most Wanted">
		<meta name="author" content="Raphael Mobis Tacla">

		<link rel="author" href="https://github.com/rmobis">
		<title>{{{ $title }}}</title>

		@include('scripts/constants')

		{{ Basset::show('bootstrap.css') }}
		{{ Basset::show('website.css') }}
		{{ Basset::show('bootstrap-resp.css') }}

		{{ Basset::show('website.js') }}
		{{ Basset::show('bootstrap.js') }}

			{{ Basset::show('dev.css') }}
			{{ Basset::show('dev.js') }}
	</head>
	<body>
		<div class="wrapper">
			<header>
				<div class="navbar navbar-inverse">
					<div class="navbar-inner straight">
						<div class="container">
							<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a href="{{ URL::route('home') }}" class="brand">Most Wanted<span class="beta-brand">BETA</span></a>
							<div class="nav-collapse collapse">
								<ul class="nav">
									<li class="divider-vertical"></li>
									<li {{Route::is('home') ? 'class="active"' : ''}}>
										<a href="{{ URL::route('home') }}">
											<i class="icon-home"></i>
											Home
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</header>
			{{ $subpage }}
			<div class="push"></div>
		</div>
		<footer class="footer">
			<div class="container">
				<p>Designed, built and ripped off of <a href="http://twitter.github.com/bootstrap/">Bootstrap</a> with all the love in the world by <a href="http://twitter.com/rmobis" target="_blank">@rmobis</a>.</p>
				<p>Code licensed under <a href="http://www.apache.org/licenses/LICENSE-2.0" target="_blank">Apache License v2.0</a>, <a href="http://glyphicons.com">Glyphicons Free</a> licensed under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
				<p>Highly inspired on <a href="http://www.tnuc.org" target="_blank">tnuC</a>, as well as on a few other tools out there!</p>

				<ul class="footer-links">
					<li><a href="http://blog.getbootstrap.com">Contact</a></li>
					<li class="muted">&middot;</li>
					<li><a href="https://github.com/rmobis/tmw/issues?state=open">Issues</a></li>
					<li class="muted">&middot;</li>
					<li><a href="https://github.com/rmobis/tmw/blob/master/CHANGELOG.md">Changelog</a></li>
				</ul>
			</div>
		</footer>
	</body>
</html>