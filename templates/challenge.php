<?php

script('twofactor_u2f', 'vendor/u2f-api');
script('twofactor_u2f', 'challenge');
style('twofactor_u2f', 'style');

?>

<input id="u2f-auth" type="hidden" value="<?php p(json_encode($_['reqs'])); ?>">

<form method="POST" id="u2f-form">
	<input id="challenge" type="hidden" name="challenge">
</form>

<img src="<?php print_unescaped(image_path('twofactor_u2f', 'u2f.svg')); ?>">
<p><?php p($l->t('Please plug in your U2F device and press the device button to authorize.')) ?></p>
<p><em><?php p($l->t('Chrome is the only browser that supports U2F devices. You need to install the "U2F Support Add-on" on Firefox to use U2F.')) ?></em></p>