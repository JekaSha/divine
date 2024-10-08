<template>
	<v-container>
		<v-form @submit.prevent="submitRequest">
			<!-- Выбор исходной валюты -->
			<v-autocomplete
				v-model="selectedCurrency"
				:items="currencies"
				item-title="currency_name"
				item-value="currency_id"
				label="Выберите валюту для отправки"
				@update:modelValue="setProtocols"
			></v-autocomplete>

			<v-autocomplete
				v-model="selectedProtocol"
				:items="selectedCurrencyProtocols"
				item-title="protocol_name"
				item-value="protocol_id"
				label="Выберите протокол"
			></v-autocomplete>

			<v-text-field
				v-model="amount"
				label="Сумма для отправки"
				type="number"
			></v-text-field>

			<!-- Выбор валюты для получения -->
			<v-autocomplete
				v-model="targetCurrency"
				:items="filteredCurrencies"
				item-title="currency_name"
				item-value="currency_id"
				label="Выберите валюту для получения"
				@update:modelValue="setTargetProtocols"
			></v-autocomplete>

			<v-autocomplete
				v-model="targetProtocol"
				:items="targetCurrencyProtocols"
				item-title="protocol_name"
				item-value="protocol_id"
				label="Протокол для получения"
			></v-autocomplete>

			<v-btn @click="fetchRate">Получить сумму</v-btn>
			<v-alert v-if="receivedAmount" type="info">
				Сумма, которую вы получите: {{ receivedAmount }}
			</v-alert>

			<v-btn @click="openWalletModal">Создать заявку</v-btn>

			<v-dialog v-model="walletDialog">
				<v-card>
					<v-card-title>Введите адрес кошелька</v-card-title>
					<v-card-text>
						<v-text-field v-model="walletAddress" label="Адрес кошелька"></v-text-field>
					</v-card-text>
					<v-card-actions>
						<v-btn @click="submitRequest">Отправить</v-btn>
						<v-btn @click="walletDialog = false">Закрыть</v-btn>
					</v-card-actions>
				</v-card>
			</v-dialog>
		</v-form>
	</v-container>
</template>

<script>
export default {
	data() {
		return {
			selectedCurrency: null,
			selectedProtocol: null,
			amount: null,
			targetCurrency: null,
			targetProtocol: null,
			currencies: [], // Данные о валютах
			filteredCurrencies: [],
			selectedCurrencyProtocols: [],
			targetCurrencyProtocols: [],
			receivedAmount: null,
			walletAddress: '',
			walletDialog: false,
		};
	},
	methods: {
		async loadCurrencies() {

			try {
				const response = await this.$axios.get('/exchange/getAllCurrencies');
				this.currencies = response.data.data;
				this.filteredCurrencies = this.currencies;
			} catch (error) {
				console.error('Ошибка при загрузке валют:', error);
			}
		},
		setProtocols() {

			if (this.selectedCurrency) {
				const selectedCurrencyData = this.currencies.find(
					(currency) => currency.currency_id === this.selectedCurrency
				);

				this.selectedCurrencyProtocols = selectedCurrencyData
				? selectedCurrencyData.protocols
				: [];

				this.selectedProtocol = null;

				// Обновляем список доступных валют для получения
				this.updateFilteredCurrencies();
			}
		},
		updateFilteredCurrencies() {
			// Фильтруем валюты, исключая выбранную исходную валюту
			this.filteredCurrencies = this.currencies.filter(
				(currency) => currency.currency_id !== this.selectedCurrency
			);
			// Если текущая целевая валюта была исключена, сбросить ее
			if (this.targetCurrency === this.selectedCurrency) {
				this.targetCurrency = null;
				this.targetProtocol = null;
				this.targetCurrencyProtocols = [];
			}
		},
		setTargetProtocols() {

			const targetCurrencyData = this.currencies.find(
				(currency) => currency.currency_id === this.targetCurrency
			);
			this.targetCurrencyProtocols = targetCurrencyData
				? targetCurrencyData.protocols
				: [];
			this.targetProtocol = null; // Сброс выбранного протокола
		},
		async fetchRate() {
			// Запрос на получение суммы, которую пользователь получит
			try {
				const response = await this.$axios.get(`/exchange/rate`, {
					params: {
						currency: this.selectedCurrency,
						protocol: this.selectedProtocol,
						to_currency: this.targetCurrency,
						to_protocol: this.targetProtocol,
						amount: this.amount,
					},
				});
				this.receivedAmount = response.data.amount; // Предполагается, что сумма приходит в поле amount
			} catch (error) {
				console.error('Ошибка при получении курса:', error);
			}
		},
		openWalletModal() {
			this.walletDialog = true;
		},
		async submitRequest() {
			// Отправить запрос на создание заявки
			try {
				const response = await this.$axios.post('/exchange/request', {
					wallet: this.walletAddress,
					amount: this.amount,
					currency: this.selectedCurrency,
					protocol: this.selectedProtocol,
					target_currency: this.targetCurrency,
					target_protocol: this.targetProtocol,
				});
				// Обработка ответа
				this.walletDialog = false;
				console.log('Заявка успешно создана:', response.data);
			} catch (error) {
				console.error('Ошибка при создании заявки:', error);
			}
		},
	},
	mounted() {
		// Загрузка доступных валют с бэкенда при монтировании компонента
		this.loadCurrencies();
	},
};
</script>
