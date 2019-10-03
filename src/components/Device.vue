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
		    <a class="icon icon-more"
			   v-on:click.stop="togglePopover"></a>
		    <div class="popovermenu"
				 :class="{open: showPopover}"
				 v-click-outside="hidePopover">
				<PopoverMenu :menu="menu"/>
		    </div>
		</span>
	</div>
</template>

<script>
	import ClickOutside from 'vue-click-outside'
	import confirmPassword from '@nextcloud/password-confirmation'
	import {PopoverMenu} from 'nextcloud-vue'

	export default {
		name: 'Device',
		props: {
			id: Number,
			name: String,
		},
		components: {
			PopoverMenu
		},
		directives: {
			ClickOutside
		},
		data () {
			return {
				showPopover: false,
				menu: [
					{
						text: t('twofactor_u2f', 'Remove'),
						icon: 'icon-delete',
						action: () => {
							confirmPassword()
								.then(() => this.$store.dispatch('removeDevice', this.id))
								.catch(console.error.bind(this))
						}
					}
				]
			}
		},
		methods: {
			togglePopover () {
				this.showPopover = !this.showPopover
			},

			hidePopover () {
				this.showPopover = false
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

	.u2f-device .popovermenu {
		right: -12px;
		top: 42px;
	}

	.icon-u2f-device {
		display: inline-block;
		background-size: 100%;
		padding: 3px;
		margin: 3px;
	}
</style>