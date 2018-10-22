<!--
  - @copyright 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @author 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
	<div v-if="step === RegistrationSteps.READY">
		<button
				v-on:click="start">{{ t('twofactor_u2f', 'Add U2F device') }}
		</button>
	</div>

	<div v-else-if="step === RegistrationSteps.U2F_REGISTRATION"
		 class="new-u2f-device">
		<span class="icon-loading-small u2f-loading"></span>
		{{ t('twofactor_u2f', 'Please plug in your U2F device and press the device button to authorize.') }}
	</div>

	<div v-else-if="step === RegistrationSteps.NAMING"
		 class="new-u2f-device">
		<span class="icon-loading-small u2f-loading"></span>
		<input type="text"
			   :placeholder="t('twofactor_u2f', 'Name your device')"
			   v-model="name">
		<button v-on:click="submit">{{ t('twofactor_u2f', 'Add') }}</button>
	</div>

	<div v-else-if="step === RegistrationSteps.PERSIST"
		 class="new-u2f-device">
		<span class="icon-loading-small u2f-loading"></span>
		{{ t('twofactor_u2f', 'Adding your device …') }}
	</div>

	<div v-else>
		Invalid registration step. This should not have happened.
	</div>
</template>

<script>
	import confirmPassword from 'nextcloud-password-confirmation'
	import u2f from 'u2f-api'

	import {
		startRegistration,
		finishRegistration
	} from '../services/RegistrationService'

	const logAndPass = (text) => (data) => {
		console.debug(text)
		return data
	}

	const RegistrationSteps = Object.freeze({
		READY: 1,
		U2F_REGISTRATION: 2,
		NAMING: 3,
		PERSIST: 4,
	})

	export default {
		name: 'AddDeviceDialog',
		props: {
			httpWarning: Boolean
		},
		data () {
			return {
				name: '',
				registrationData: {},
				RegistrationSteps,
				step: RegistrationSteps.READY,
			}
		},
		methods: {
			start () {
				this.step = RegistrationSteps.U2F_REGISTRATION

				return confirmPassword()
					.then(this.getRegistrationData)
					.then(this.register)
					.then(() => this.step = RegistrationSteps.NAMING)
					.catch(console.error.bind(this))
			},

			getRegistrationData () {
				return startRegistration()
					.catch(err => {
						console.error('Error getting u2f registration data from server', err)
						throw new Error(t('twofactor_u2f', 'Server error while trying to add U2F device'))
					})
			},

			register ({req, sigs}) {
				console.debug('starting u2f registration')

				return u2f.register([req], sigs)
					.then(data => {
						console.debug('u2f registration was successful', data)

						if (data.errorCode && data.errorCode !== 0) {
							return this.rejectRegistration(data)
						}

						this.registrationData = data
					})
			},

			rejectRegistration (data) {
				// https://developers.yubico.com/U2F/Libraries/Client_error_codes.html
				switch (data.errorCode) {
					case 4:
						// 4 - DEVICE_INELIGIBLE
						Promise.reject(new Error(t('twofactor_u2f', 'U2F device is already registered (error code {errorCode})', {
							errorCode: data.errorCode
						})));
						break;
					case 5:
						// 5 - TIMEOUT
						Promise.reject(new Error(t('twofactor_u2f', 'U2F device registration timeout reached (error code {errorCode})', {
							errorCode: data.errorCode
						})));
						break;
					default:
						// 1 - OTHER_ERROR
						// 2 - BAD_REQUEST
						// 3 - CONFIGURATION_UNSUPPORTED
						Promise.reject(new Error(t('twofactor_u2f', 'U2F device registration failed (error code {errorCode})', {
							errorCode: data.errorCode
						})));
				}
			},

			submit () {
				this.step = RegistrationSteps.PERSIST

				return confirmPassword()
					.then(logAndPass('confirmed password'))
					.then(this.saveRegistrationData)
					.then(logAndPass('registration data saved'))
					.then(() => this.reset())
					.then(logAndPass('app reset'))
					.catch(console.error.bind(this))
			},

			saveRegistrationData () {
				const data = this.registrationData
				data.name = this.name

				return finishRegistration(data)
					.then(device => this.$store.commit('addDevice', device))
					.then(logAndPass('new device added to store'))
					.catch(err => {
						console.error('Error persisting u2f registration', err)
						throw new Error(t('twofactor_u2f', 'Server error while trying to complete U2F device registration'))
					})
			},

			reset () {
				this.name = ''
				this.registrationData = {}
				this.step = RegistrationSteps.READY
			}
		}
	}
</script>

<style scoped>
	.u2f-loading {
		display: inline-block;
		vertical-align: sub;
		margin-left: 2px;
		margin-right: 2px;
	}

	.new-u2f-device {
		line-height: 300%;
	}
</style>