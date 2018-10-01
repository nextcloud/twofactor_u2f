<?php

script('twofactor_u2f', 'build/challenge');
style('twofactor_u2f', 'style');

?>

<input id="u2f-auth" type="hidden" value="<?php p(json_encode($_['reqs'])); ?>">

<form method="POST" id="u2f-form">
	<input id="challenge" type="hidden" name="challenge">
</form>

<img class="two-factor-icon" src="<?php print_unescaped(image_path('twofactor_u2f', 'app.svg')); ?>" alt="">
<p id="u2f-info">
<?php p($l->t('Plug in your U2F device and press the device button to authorize.')) ?>
</p>
<p id="u2f-error"
   style="display: none">
	<strong><?php p($l->t('An error occurred. Please try again.')) ?></strong>
</p>
<p>
	<em>
		<?php p($l->t('In Firefox, you need to install the "U2F Support Add-on" to use U2F. This is not needed in Chrome.')) ?>
		<p id="u2f-http-warning"
		      style="display: none">
			<?php p($l->t('You are accessing this site via an insecure connection. Browsers might therefore refuse the U2F authentication.')) ?>
		</p>
	</em>
</p>
