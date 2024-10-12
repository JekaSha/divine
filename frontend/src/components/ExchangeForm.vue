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


			<v-alert v-if="receivedAmount" type="info">
				<p>Обменный курс <b>{{selectedCurrencyName}}:{{targetCurrencyName}}</b> = {{ rate }}</p>
				<p>Сумма, которую вы получите: {{ receivedAmount }}</p>
			</v-alert>

			<v-btn @click="openWalletModal" :disabled="!isRateFetched">Создать заявку</v-btn>

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

			<v-dialog v-model="showConfirmationModal">
				<v-card>
					<v-card-title>Подтверждение транзакции</v-card-title>
					<v-card-text>
						<p>Адрес кошелька: <strong>{{ walletAddress }}</strong></p>
						<p>Сумма для отправки: <strong>{{ amount }}</strong></p>
						<p>Сумма, которую вы получите: <strong>{{ receivedAmount }}</strong></p>
						<p>Время действия транзакции: <strong>{{ expiryTime }}</strong></p>
					</v-card-text>
					<v-card-actions>
						<v-btn @click="confirmTransaction">Подтвердить</v-btn>
						<v-btn @click="showConfirmationModal = false">Закрыть</v-btn>
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
			rate: 0,
			isRateFetched: false,
			showConfirmationModal: false,
		};
	},

	computed: {
		selectedCurrencyName() {
			const currency = this.currencies.find(currency => currency.currency_id === this.selectedCurrency);
			return currency ? currency.currency_name : '';
		},
		targetCurrencyName() {
			const currency = this.currencies.find(currency => currency.currency_id === this.targetCurrency);
			return currency ? currency.currency_name : '';
		},
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

				this.updateFilteredCurrencies();
			}
		},
		updateFilteredCurrencies() {
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

		openWalletModal() {
			this.walletDialog = true;
		},
		async submitRequest() {
			// Validate necessary data before proceeding
			if (!this.selectedCurrency || !this.selectedProtocol || !this.amount || !this.targetCurrency || !this.targetProtocol || !this.walletAddress) {
				console.error('Please fill in all required fields.');
				return;
			}

			// Step 1: Submit the order and create the wallet in a single request
			try {
				const response = await this.$axios.get('/exchange/order', {
					params: {
						amount: this.amount,
						currency: this.selectedCurrency,
						protocol: this.selectedProtocol,
						target_currency: this.targetCurrency,
						target_protocol: this.targetProtocol,
						wallet_address: this.walletAddress, // Include user-provided wallet address
					}
				});


				// Step 2: Retrieve wallet address, received amount, and expiry time from the response
				const { wallet_address, received_amount, expiry_time } = response.data.data;

				// Step 3: Show the information in a modal
				this.walletAddress = wallet_address;
				this.receivedAmount = received_amount;
				this.expiryTime = expiry_time;

				this.showConfirmationModal = true; // Show the modal
			} catch (error) {
				console.error('Error creating order:', error);
			}
		},

		async fetchReceivedAmount() {
			if (this.selectedCurrency && this.targetCurrency && this.amount) {
				try {
					const response = await this.$axios.get('/exchange/rate', {
						params: {
							currency: this.selectedCurrency,
							to_currency: this.targetCurrency,
							amount: this.amount,
						}
					});
					if (response.data.status == 'success') {
						const data = response.data.data;
						this.receivedAmount = data.receivedAmount; // Предполагается, что API возвращает сумму получения
						this.rate = data.rate;
						this.isRateFetched = true; // Устанавливаем true, когда курс получен
					} else {
						this.receivedAmount = 0;
						this.isRateFetched = false; // Устанавливаем false, если курс не получен
					}
				} catch (error) {
					console.error('Ошибка при получении суммы:', error);
					this.isRateFetched = false; // Устанавливаем false при ошибке
				}
			}
		},
	},

	watch: {

		selectedCurrency(newValue) {
			this.receivedAmount = null; // Сбрасываем сумму
			this.isRateFetched = false; // Дизаблим кнопку
			this.fetchReceivedAmount();
		},
		amount(newValue) {
			this.receivedAmount = null; // Сбрасываем сумму
			this.isRateFetched = false; // Дизаблим кнопку
			this.fetchReceivedAmount();
		},
		targetCurrency(newValue) {
			this.receivedAmount = null; // Сбрасываем сумму
			this.isRateFetched = false; // Дизаблим кнопку
			this.fetchReceivedAmount();
		},


	},
	mounted() {
		// Загрузка доступных валют с бэкенда при монтировании компонента
		this.loadCurrencies();
	},


};
</script>
