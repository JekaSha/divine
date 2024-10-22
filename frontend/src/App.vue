<template>
	<v-app>
		<v-main>
			<ExchangeForm />
		</v-main>
	</v-app>
</template>

<script>
import ExchangeForm from './components/ExchangeForm.vue'

export default {
	name: 'App',

	components: {
		ExchangeForm,
	},

	data() {
		return {

		};
	},
}
</script>
