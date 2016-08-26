/* global OC */

(function (OC) {
    'use strict';

    OC.Settings = OC.Settings || {};
    OC.Settings.TwoFactorU2F = OC.Settings.TwoFactorU2F || {};

    $(function () {
        var view = new OC.Settings.TwoFactorU2F.View({
            el: $('#twofactor-u2f-settings')
        });
        view.render();
    });
})(OC);

