import Vue from 'vue'
import Vuex from 'vuex'

import {removeRegistration} from './services/RegistrationService'

Vue.use(Vuex)

export const mutations = {
	addDevice (state, device) {
		state.devices.push(device)
		state.devices.sort((d1, d2) => d1.name.localeCompare(d2.name))
	},

	removeDevice (state, id) {
		state.devices = state.devices.filter(device => device.id !== id)
	}
}

export const actions = {
	removeDevice ({state, commit}, id) {
		const device = state.devices[id]

		commit('removeDevice', id)

		removeRegistration(id)
			.catch(err => {
				// Rollback
				commit('addDevice', device)

				throw err
			})
	}
}

export const getters = {}

export default new Vuex.Store({
	strict: process.env.NODE_ENV !== 'production',
	state: {
		devices: []
	},
	getters,
	mutations,
	actions
})
