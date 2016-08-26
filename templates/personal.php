<?php

script('twofactor_u2f', 'vendor/u2f-api');
script('twofactor_u2f', 'settings');
script('twofactor_u2f', 'settingsview');

?>

<div class="section">
    <h2><?php p($l->t('U2F Second-factor Auth')); ?></h2>
    <div id="twofactor-u2f-settings"></div>
</div>
