<?php
script('twofactor_u2f', 'build/settings');
style('twofactor_u2f', 'style');
?>

<div class="section">
	<h2 data-anchor-name="u2f-second-factor-auth"><?php p($l->t('U2F second-factor auth')); ?></h2>
	<div id="twofactor-u2f-settings">
		<span class="icon-loading-small u2f-loading"></span>
		<span><?php p($l->t('Loading your devices …')); ?></span>
	</div>
	<p class="utf-register-info" style="display: none;">
		<?php p($l->t('Please plug in your U2F device and press the device button to authorize.')) ?>
	</p>
	<p class="utf-register-info" style="display: none;">
		<em>
			<?php p($l->t('Chrome is the only browser that supports U2F devices. You need to install the "U2F Support Add-on" on Firefox to use U2F.')) ?>
			<p id="u2f-http-warning"
			   style="display: none">
				   <?php p($l->t('You are accessing this site via an insecure connection. Browsers might therefore refuse the U2F authentication.')) ?>
			</p>
		</em>
	</p>
	<p class="utf-register-success" style="display: none;">
		<span class="icon-checkmark-color" style="width: 16px;"></span><?php p($l->t('U2F device successfully registered.')) ?>
	</p>
</div>
