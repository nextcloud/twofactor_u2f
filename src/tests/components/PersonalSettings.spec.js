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

import {shallowMount, createLocalVue} from '@vue/test-utils'
import Vuex from 'vuex'

import Nextcloud from '../../mixins/Nextcloud'

const localVue = createLocalVue()

localVue.use(Vuex)
localVue.mixin(Nextcloud)

import PersonalSettings from '../../components/PersonalSettings'

describe('Device component', () => {
	var actions
	var store

	beforeEach(() => {
		actions = {}
		store = new Vuex.Store({
			state: {
				devices: []
			},
			actions
		})
	})

	it('shows text if no devices are configured', () => {
		const settings = shallowMount(PersonalSettings, {
			store,
			localVue
		})

		expect(settings.text()).to.contain('No U2F devices configured')
	})

	it('shows no info text if devices are configured', () => {
		store.state.devices.push({
			id: 1,
			name: 'a'
		})
		const settings = shallowMount(PersonalSettings, {
			store,
			localVue
		})

		expect(settings.text()).to.not.contain('No U2F devices configured')
	})

	it('shows a HTTP warning', () => {
		const settings = shallowMount(PersonalSettings, {
			store,
			localVue,
			propsData: {
				httpWarning: true
			}
		})

		expect(settings.text()).to.contain('You are accessing this site via an')
	})
})
