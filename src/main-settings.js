/*
 * @copyright 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

import store from './store'
import Vue from 'vue'

import Nextcloud from './mixins/Nextcloud'
import { loadState } from '@nextcloud/initial-state'

Vue.mixin(Nextcloud)

const devices = loadState('twofactor_u2f', 'devices');
devices.sort((d1, d2) => {
	if (!d1.name) {
		return 1
	} else if (!d2.name) {
		return -1
	} else {
		return d1.name.localeCompare(d2.name)
	}
})
store.replaceState({
	devices
})

import PersonalSettings from './components/PersonalSettings'

const View = Vue.extend(PersonalSettings)
new View({
	propsData: {
		httpWarning: document.location.protocol !== 'https:',
	},
	store,
}).$mount('#twofactor-u2f-settings')
