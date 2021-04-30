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
		<p v-if="devices.length === 0">{{ t('twofactor_u2f', 'No U2F devices configured. You are not using U2F as second factor at the moment.') }}</p>
		<p v-else>{{ t('twofactor_u2f', 'The following devices are configured for U2F second-factor authentication:') }}</p>
		<Device v-for="device in devices"
				   :key="device.id"
				   :id="device.id"
				   :name="device.name" />

		<AddDeviceDialog :httpWarning="httpWarning" />
		<p v-if="notSupported">
			{{ t('twofactor_u2f', 'Your browser does not support U2F.') }}
			{{ t('twofactor_u2f', 'Please use an up-to-date browser that supports U2F devices, such as Chrome, Edge, Firefox, Opera or Safari.') }}
		</p>
		<p v-if="httpWarning"
		   id="u2f-http-warning">
			{{ t('twofactor_u2f', 'You are accessing this site via an insecure connection. Browsers might therefore refuse the U2F authentication.') }}
		</p>
	</div>
</template>

<script>
	import u2f from 'u2f-api'

	import AddDeviceDialog from './AddDeviceDialog'
	import Device from './Device'

	export default {
		name: 'PersonalSettings',
		props: {
			httpWarning: Boolean
		},
		components: {
			AddDeviceDialog,
			Device
		},
		data() {
			return {
				notSupported: !u2f.isSupported()
			}
		},
		computed: {
			devices() {
				return this.$store.state.devices
			}
		}
	}
</script>

<style scoped>

</style>
