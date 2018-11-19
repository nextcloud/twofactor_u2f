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

Vue.mixin(Nextcloud)

const initialStateElement = document.getElementById('twofactor-u2f-req')
const req = JSON.parse(initialStateElement.value)

import Challenge from './components/Challenge'

const View = Vue.extend(Challenge)
new View({
	propsData: {
		req,
		httpWarning: document.location.protocol !== 'https:',
	},
	store,
}).$mount('#twofactor-u2f-challenge')
