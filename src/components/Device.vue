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
	<div class="u2f-device" :data-u2f-id="id">
		<span class="icon-u2f-device"></span>
		{{name || t('twofactor_u2f', 'Unnamed device') }}
		<span class="more">
			<Actions :forceMenu="true">
				<ActionButton icon="icon-delete" @click="onDelete">
					{{ t('twofactor_u2f', 'Remove') }}
				</ActionButton>
			</Actions>
		</span>
	</div>
</template>

<script>
	import ClickOutside from 'vue-click-outside'
	import confirmPassword from '@nextcloud/password-confirmation'
	import Actions from '@nextcloud/vue/dist/Components/Actions'
	import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'

	export default {
		name: 'Device',
		props: {
			id: Number,
			name: String,
		},
		components: {
			ActionButton,
			Actions,
		},
		directives: {
			ClickOutside
		},
		methods: {
			async onDelete() {
				await confirmPassword()
				try {
					await this.$store.dispatch('removeDevice', this.id)
				} catch (e) {
					console.error('could not delete device', e)
				}
			}
		}
	}
</script>

<style scoped>
	.u2f-device {
		line-height: 300%;
		display: flex;
	}

	.u2f-device .more {
		position: relative;
	}

	.u2f-device .more .icon-more {
		display: inline-block;
		width: 16px;
		height: 16px;
		padding-left: 20px;
		vertical-align: middle;
		opacity: .7;
	}

	.icon-u2f-device {
		display: inline-block;
		background-size: 100%;
		padding: 3px;
		margin: 3px;
	}
</style>
