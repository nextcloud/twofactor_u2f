<?php

script('twofactor_u2f', 'vendor/u2f-api');
script('twofactor_u2f', 'challenge');

?>

<input id="u2f-auth" type="hidden" value="<?php p(json_encode($_['reqs'])); ?>">

<form method="POST" id="u2f-form">
    <input id="challenge" type="hidden" name="challenge">
</form>