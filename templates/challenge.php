<?php

script('twofactor_u2f', 'vendor/u2f-api');
script('twofactor_u2f', 'challenge');
style('twofactor_u2f', 'style');

?>

<input id="u2f-auth" type="hidden" value="<?php p(json_encode($_['reqs'])); ?>">

<form method="POST" id="u2f-form">
	<input id="challenge" type="hidden" name="challenge">
</form>

<img src="<?php print_unescaped(image_path('twofactor_u2f', 'app.svg')); ?>">
<p id="u2f-info">
<?php p($l->t('Please plug in your U2F device and press the device button to authorize.')) ?>
</p>
<p id="u2f-error"
   style="display: none">
	<strong><?php p($l->t('An error occurred. Please try again.')) ?></strong>
</p>
<p>
	<em>
		<?php p($l->t('Install the "U2F Support Add-on" on Firefox to use U2F, this is not needed on Chrome.')) ?>
		<p id="u2f-http-warning"
		      style="display: none">
			<?php p($l->t('You are accessing this site via an insecure connection. Browsers might therefore refuse the U2F authentication.')) ?>
		</p>
	</em>
</p>
