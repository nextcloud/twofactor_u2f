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
	<div>
		<form method="POST"
			  ref="challengeForm">
			<input id="challenge"
				   type="hidden"
				   name="challenge"
				   v-model="challenge">
		</form>

		<p id="u2f-info"
		   v-if="error">
			<strong>
				{{ t('twofactor_u2f', 'An error occurred: {msg}', {msg: this.error}) }}
			</strong>
			<br>
			<button class="btn"
					@click="sign">
				{{ t('twofactor_u2f', 'Retry') }}
			</button>
		</p>
		<p id="u2f-info"
		   v-else>
			{{ t('twofactor_u2f', 'Plug in your U2F device and press the device button to authorize.') }}
		</p>
		<p id="u2f-error"
		   style="display: none">
			<strong>{{ t('twofactor_u2f', 'An error occurred. Please try again.')}}</strong>
		</p>

		<p v-if="notSupported">
			<em>
			{{ t('twofactor_u2f', 'Your browser does not support U2F.') }}
			{{ t('twofactor_u2f', 'Please use an up-to-date browser that supports U2F devices, such as Chrome, Edge, Firefox, Opera or Safari.') }}
			</em>
		</p>
		<p v-else-if="httpWarning"
		   id="u2f-http-warning">
			<em>
			{{ t('twofactor_u2f', 'You are accessing this site via an insecure connection. Browsers might therefore refuse the U2F authentication.') }}
			</em>
		</p>
	</div>
</template>

<script>
	import u2f from 'u2f-api'

	export default {
		name: 'Challenge',
		props: {
			req: {
				type: Array,
				required: true,
			},
			httpWarning: {
				type: Boolean,
				required: true,
			}
		},
		data () {
			return {
				notSupported: !u2f.isSupported(),
				challenge: '',
				error: undefined,
			}
		},
		mounted () {
			this.sign()
				.catch(console.error.bind(this))
		},
		methods: {
			sign () {
				console.debug('Starting u2f.sign', this.req)

				this.error = undefined

				return u2f.sign(this.req)
					.then(challenge => {
						this.challenge = JSON.stringify(challenge)

						return this.$nextTick(() => {
							this.$refs.challengeForm.submit()
						})
					})
					.catch(err => {
						console.error('could not sign u2f challenge', err.metaData)

						this.error = err.message || 'Unknown error'

						throw err
					})
			}
		}
	}
</script>
