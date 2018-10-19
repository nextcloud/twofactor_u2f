<?php
script('twofactor_u2f', 'settings');
style('twofactor_u2f', 'style');
?>

<input type="hidden" id="twofactor-u2f-initial-state" value="<?php p($_['state']); ?>">

<div id="twofactor-u2f-settings"></div>
