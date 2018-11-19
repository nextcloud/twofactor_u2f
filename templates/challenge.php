<?php

script('twofactor_u2f', 'challenge');

?>

<img class="two-factor-icon" src="<?php print_unescaped(image_path('twofactor_u2f', 'app.svg')); ?>" alt="">

<input id="twofactor-u2f-req" type="hidden" value="<?php p(json_encode($_['reqs'])); ?>">
<div id="twofactor-u2f-challenge"></div>
