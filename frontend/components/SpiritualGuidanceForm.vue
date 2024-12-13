<template>

		<v-container class="header-container py-2">
			<!-- Верхний ряд: Заголовок и профиль в одну строку -->
			<v-row align="right" justify="space-between">

				<div class="profile-header">
					<v-avatar v-if="user" size="30" class="mr-2">
						<v-icon>mdi-account-circle</v-icon>
					</v-avatar>
					<div v-if="user" class="profile-details">
						<span class="user-name">{{ user.name || "Guest" }}</span>
						<span class="user-email">{{ user.email }}</span>
					</div>
					<v-btn v-else color="primary" @click="redirectToLogin" class="login-btn">
						Login
					</v-btn>
				</div>
			</v-row>

		</v-container>

	<v-container class="py-12">

		<!-- Main Content -->
		<v-row justify="center" class="mb-1">
			<v-col cols="12" md="8" class="text-center">
				<h1 class="text-h4 font-weight-bold">{{ $t("index_title") }}</h1>
				<p class="text-body-1 mt" v-html="$t('index_description')"></p>
			</v-col>
		</v-row>


		<v-row justify="center">
			<v-col
				cols="12"
				md="10"
				lg="8"
				:class="{ 'blurred-form': isLoading || errorMessage }"
				style="position: relative;"
			>
				<div v-if="isLoading || errorMessage" class="overlay">
					<div v-if="isLoading && !errorMessage" class="loading-spinner"></div>
				</div>

				<div v-if="errorMessage" class="error-container">
					<p class="error-text">{{ errorMessage }}</p>
					<v-btn color="primary" @click="clearError">Понятно</v-btn>
				</div>


				<!-- Tabs for Navigation -->
				<v-tabs v-if="stepsHistory.length > 1" v-model="activeTab" class="mb-4">
					<v-tab v-for="(step, index) in stepsHistory" :key="index">
						{{ step.title }}
					</v-tab>
				</v-tabs>

				<v-card class="pa-6" elevation="3">
					<!-- Loader or Error Message -->

					<v-card-title v-if="step === 1" class="text-h5 font-weight-bold">
						{{ $t('step_1_caption') }}
					</v-card-title>
					<v-card-title v-if="step === 2" class="text-h5 font-weight-bold">
						✝️ Enter Your Email
					</v-card-title>
					<v-card-title v-if="step === 3" class="text-h5 font-weight-bold">
						{{ $t('step_3_caption') }}
					</v-card-title>
					<v-card-title v-if="step === 4" class="text-h5 font-weight-bold">
						✝️ Your Personalized Answer
					</v-card-title>

					<v-tabs-items v-model="activeTab">
						<!-- Step 1 -->
						<v-tab-item v-if="step === 1" value="0">
							<v-row justify="center" class="mb-1">
								<v-form v-if="step === 1">
							<v-textarea
								:label="$t('step_1_textarea_label')"
								required
								outlined
								rows="6"
								v-model="challenge"
							></v-textarea>
							<p class="text-caption secure-info">
								Your information is secure and will be used solely to deliver your personalized guidance.
							</p>
							<v-alert
								class="mt-4"
								border="left"
								elevation="0"
								icon="mdi-information-outline"
								style="background-color: #f8f9fa; color: #495057; border-left: 4px solid #6c757d; font-size:12px"
							>
								Please avoid sharing sensitive data such as passwords or credit card info in this form.
							</v-alert>
							<v-btn
								color="primary"
								class="mt-6"
								block
								:disabled="!challenge || isLoading"
								@click="submitChallenge"
							>
								{{ $t('step_1_button_action') }}
							</v-btn>
						</v-form>

							</v-row>
						</v-tab-item>

						<v-tab-item v-if="step === 2" value="1">
							<v-row justify="center" class="mb-1">
								<!-- Step 2 -->
								<v-form v-if="step === 2">

									<p class="text-caption secure-info">
										We are sending to your email the response to your request:
										"<strong>{{ challenge }}</strong>"
									</p>
									<v-text-field
										label="Email"
										required
										outlined
										type="email"
										v-model="email"
									></v-text-field>

									<!-- Прогресс-бар отображается, пока выполняется запрос -->
									<div v-if="!isLoading && !responseData && !errorMessage" class="progress-bar-container">
										<v-progress-linear
											:indeterminate="false"
											:buffer-value="progress"

											height="22"
											class="mt-2 custom-progress-bar"
										></v-progress-linear>
									</div>

									<div v-if="processedResponse.truncated" class="mt-4 response-container">
										<p class="question">
											<span class="question-label">Question:</span> {{ responseData.request }}
										</p>
										<p class="response">
											<span class="response-label">Response:</span>
											<span v-html="processedResponse.truncated"></span>
											<span v-if="processedResponse.percentage < 100" class="read-more">
			... <a href="#subscription-plans" class="read-more-link">Read more</a>
		</span>
										</p>
									</div>

									<!-- Ошибка отображается при наличии -->
									<div v-if="errorMessage" class="mt-4">
										<v-alert type="error">{{ errorMessage }}</v-alert>
									</div>

									<v-btn
										color="primary"
										class="mt-6"
										block
										:disabled="!email || isLoading"
										@click="submitEmail"
									>
										Send
									</v-btn>
								</v-form>
							</v-row>
						</v-tab-item>


						<v-tab-item v-if="step === 3" value="2">
							<v-row justify="center" class="mb-1">
								<!-- Step 3 -->
								<div v-if="step === 3">
									<div v-if="processedResponse.truncated" class="mt-4 response-container">
										<p class="question">
											<span class="question-label">Question:</span> {{ responseData.request }}
										</p>
										<p class="response">
											<span class="response-label">Response:</span>
											<span v-html="processedResponse.truncated"></span>
											<span v-if="processedResponse.percentage < 100" class="read-more">
			... <a href="#subscription-plans" class="read-more-link">Read more</a>
		</span>
										</p>
									</div>

									<v-btn
										color="primary"
										class="mt-6"
										block
										@click="showFullResponse"
									>
										{{ $t('step_3_button_action' ) }}
									</v-btn>
						</div>
							</v-row>
						</v-tab-item>


						<v-tab-item v-if="step === 4" value="3">

							<v-row justify="center" class="mb-1">
								<!-- Step 4 -->
								<div v-if="step === 4">

									<div v-if="processedResponse.truncated" class="mt-4 response-container">
										<p class="question">
											<span class="question-label">Question:</span> {{ responseData.request }}
										</p>
										<p class="response">
											<span class="response-label">Response:</span>
											<span v-html="processedResponse.truncated"></span>
											<span v-if="processedResponse.percentage < 100" class="read-more">
			... <a href="#subscription-plans" class="read-more-link">Read more</a>
		</span>
										</p>
										<div class="response-percentage" id="subscription-plans">
											<v-icon class="response-icon" color="blue">mdi-information-outline</v-icon>
											<span>
		This is only <strong>{{ processedResponse.percentage }}%</strong> of the full response.
		To view the complete <strong>100%</strong> response,
		<a href="#subscription-plans" class="scroll-link">please subscribe to one of the plans below</a>.
	</span>
										</div>
									</div>

									<p class="subscription-message" v-html="$t('step_4_attention')"></p>
									<v-row>
										<v-col v-for="(pkg, index) in packages" :key="index" cols="12" md="4">
											<v-card class="pa-4 text-center subscription-card" outlined>
												<v-icon class="subscription-icon" color="yellow">mdi-star-circle</v-icon>
												<p class="subscription-name">{{ pkg.name }}</p>
												<p class="subscription-price">${{ pkg.price }}/month</p>
												<p class="subscription-details">{{ pkg.requests }} requests per month</p>
												<v-btn
													color="primary"
													block
													@click="choosePlan(pkg.id)"
													class="subscription-btn"
												>
													Choose
												</v-btn>
											</v-card>
										</v-col>
									</v-row>
						</div>
							</v-row>
						</v-tab-item>

					</v-tabs-items>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useSession } from '~/composables/useSession';
import { useToken } from '~/composables/useToken';
import { useRoute } from 'vue-router';


const step = ref(1);
const challenge = ref("");
const email = ref("");
const user = ref(null)
const isLoading = ref(false);
const errorMessage = ref(null); // Состояние для хранения сообщения об ошибке
const identifier = ref(generateIdentifier());
const { $axios } = useNuxtApp();

const packages = ref([]);


const progress = ref(0);
const responseData = ref(null);
const processedResponse = computed(() => {
	if (!responseData.value?.response) return { truncated: "", percentage: 0 };

	let response = responseData.value.response;
	const totalLength = response.length;
	let visibleLength = totalLength;

	// Обрезаем текст в зависимости от текущего шага
	if (step.value === 2) {
		visibleLength = Math.min(200, totalLength);
		response = response.slice(0, 200) + "...";
	} else if (step.value === 3) {
		visibleLength = Math.min(750, totalLength);
		response = response.slice(0, 750) + "...";
	} else if (step.value === 4) {
		visibleLength = Math.min(1250, totalLength);
		response = response.slice(0, 1250) + "...";
	}

	response = response.replace(/###\s*(.+)/g, "<h3>$1</h3>");
	response = response.replace(/##\s*(.+)/g, "<h2>$1</h2>");
	response = response.replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>"); // жирный текст
	response = response.replace(/-\s(.+)/g, "<li>$1</li>"); // списки

	// Рассчитываем процент видимого текста
	const percentage = Math.round((visibleLength / totalLength) * 100);

	return { truncated: response, percentage };
});



const stepsHistory = ref([{ title: "Describe Your Challenge", step: 1 }]);
const activeTab = ref(0);

const { getSession } = useSession();
const session = getSession();


watch(step, (newStep) => {
	console.log('watch: step changed to', newStep);
	if (!stepsHistory.value.some((s) => s.step === newStep)) {
		stepsHistory.value.push({
			title: getStepTitle(newStep),
			step: newStep,
		});
	}

	if (step.value === 4) {
		getLoadPackages();
	}

	activeTab.value = stepsHistory.value.findIndex((s) => s.step === newStep);
	console.log('activeTab set to', activeTab.value);
});


watch(activeTab, (newTab) => {
	console.log('activeTab changed:', newTab);
	if (stepsHistory.value[newTab]) {
		const selectedStep = stepsHistory.value[newTab].step;
		console.log('selectedStep:', selectedStep);
		if (selectedStep) {
			step.value = selectedStep;
		}
	} else {
		console.warn('Invalid activeTab index:', newTab);
	}
});


// Получение заголовка шага
function getStepTitle(step) {
	switch (step) {
		case 1:
			return "Describe Your Challenge";
		case 2:
			return "Enter Your Email";
		case 3:
			return "Response Sent";
		case 4:
			return "Your Personalized Answer";
		default:
			return "Step";
	}
}

async function choosePlan(packageId) {
	const { apiBaseUrl } = useRuntimeConfig().public;

	try {
		isLoading.value = true;
		const response = await $axios.get(`/invoice/create`, {
			packageId: packageId
		});

		if (response.status === 200 && response.data.status === "success") {
			const invoice = response.data.data.invoice;
			const url = `${apiBaseUrl}/r/invoice/${invoice.hash}`
			//alert(url);
			window.location.href = url;
		} else {
			throw new Error(response.data.message || "Failed to create invoice");
		}
	} catch (error) {
		errorMessage.value = error.response?.data?.message || error.message;
	} finally {
		isLoading.value = false;
	}

}


async function getLoadPackages() {
	try {
		isLoading.value = true;
		const response = await $axios.get(`/challenges/packages/`, {
		});

		if (response.status === 200 && response.data.status === "success") {
			packages.value = response.data.data.packages;
		} else {
			throw new Error(response.data.message || "Failed to load packages");
		}
	} catch (error) {
		errorMessage.value = error.response?.data?.message || error.message;
	} finally {
		isLoading.value = false;
	}
}

async function submitChallenge() {
	try {

		step.value = 2;
		isLoading.value = false;
		errorMessage.value = null;
		progress.value = 0; // Сбрасываем прогресс
		stepsHistory.value.push({ title: "Enter Your Email", step: 2 });
		// Запускаем эмуляцию прогресса
		const interval = setInterval(() => {
			if (progress.value < 90) {
				progress.value += 10;
			}
		}, 2500);



		// Выполняем запрос
		const response = await $axios.post(`/challenges/answer/1/${session}/`, {
			request: challenge.value,
		});

		clearInterval(interval); // Останавливаем прогресс

		if (response.status === 200 && response.data.status === "success") {
			responseData.value = response.data.data; // Сохраняем ответ
			progress.value = 100; // Завершаем прогресс
		} else {
			throw new Error(response.data.message || "Unknown error");
		}
	} catch (error) {
		errorMessage.value = error.response?.data?.message || error.message;
		progress.value = 0; // Сбрасываем прогресс при ошибке
	} finally {
		isLoading.value = false; // Снимаем флаг загрузки
	}
}


async function submitEmail() {
	try {
		isLoading.value = true;
		errorMessage.value = null; // Сбрасываем ошибку
		const response = await $axios.post(`/challenges/sendToEmail/${session}`, {
			email: email.value,
		});
		if (response.status === 200) {
			step.value = 3; // Переход на шаг 3
		} else {
			throw new Error(response.data.message || "Unknown error");
		}
	} catch (error) {
		errorMessage.value = error.response?.data?.message || error.message;
	} finally {
		isLoading.value = false;
	}
}

async function getStepData(setStep) {

	const route = useRoute()

	if (route.query.token) {
		localStorage.setItem('authToken', route.query.token)
	}

	try {
		step.value = setStep;
		isLoading.value = true;
		errorMessage.value = null;
console.log('step', step.value);
		const response = await $axios.post(`/challenges/session/${session}/`, {
		});

		if (response.status === 200 && response.data.status === "success") {
			responseData.value = response.data.data;
			if (response.data.data.user) {
				user.value = response.data.data.user
			}

			if (response.data.data.chat?.length > 0) {
				responseData.value = response.data.data.chat[0];
			} else {
				responseData.value = null;
			}
			console.log('responseData', responseData.value);

		} else {
			throw new Error(response.data.message || "Unknown error");
		}
	} catch (error) {
		errorMessage.value = error.response?.data?.message || error.message;
	} finally {
		isLoading.value = false;
	}

}

function clearError() {
	errorMessage.value = null; // Сброс ошибки
}

function showFullResponse() {
	step.value = 4; // Переход на шаг 4
}

function generateIdentifier() {
	return (
		Math.random().toString(36).substr(2, 9) + "-" + new Date().getTime()
	);
}

onMounted(() => {
	const route = useRoute();
	const routeStep = parseInt(route.params.step, 10);

	console.log('Initializing step from route:', routeStep);

	if (routeStep && !isNaN(routeStep)) {
		step.value = routeStep;
		getStepData(routeStep);
	} else {
		console.warn('Invalid routeStep, defaulting to 1');
		step.value = 1;
	}

	const links = document.querySelectorAll('.scroll-link');
	links.forEach(link => {
		link.addEventListener('click', (e) => {
			e.preventDefault();
			const targetId = link.getAttribute('href');
			const targetElement = document.querySelector(targetId);
			if (targetElement) {
				targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		});
	});
});




</script>

<style scoped>
/* Размытие для фона */
.overlay {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(255, 255, 255, 0.5);
	backdrop-filter: blur(5px); /* Применяем размытие */
	z-index: 10; /* Слой размытия */
	display: flex; /* Добавляем flexbox */
	align-items: center; /* Центрируем по вертикали */
	justify-content: center; /* Центрируем по горизонтали */
	pointer-events: none; /* Блокируем взаимодействие */
}

/* Сообщение об ошибке */
.error-container {
	position: absolute; /* Ограничиваем область формы */
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: white;
	padding: 20px;
	border-radius: 8px;
	text-align: center;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
	z-index: 20; /* Поверх размытия */
	pointer-events: auto; /* Включаем взаимодействие */
}

.error-text {
	color: #d32f2f;
	font-size: 1.2rem;
	margin-bottom: 20px;
}

.loading-spinner {
	width: 40px;
	height: 40px;
	border: 4px solid rgba(0, 0, 0, 0.1);
	border-top: 4px solid #000;
	border-radius: 50%;
	animation: spin 1s linear infinite;
}

@keyframes spin {
	0% {
		transform: rotate(0deg);
	}
	100% {
		transform: rotate(360deg);
	}
}

.v-progress-linear {
	background-color: #e9ecef;
	width: 100%;
	margin: 8px 0;
}
.v-progress-linear__buffer {
	background-color: #6c757d;
}
.progress-bar-container {
	width: 100%; /* Контейнер растянут на всю ширину */
}

.v-form {
	width: 100%; /* Заставляем форму растянуться */
}

.custom-progress-bar .v-progress-linear__bar {
	background-color: #ff0000 !important; /* Яркий красный */
	opacity: 1 !important; /* Убираем прозрачность */
}

.custom-progress-bar .v-progress-linear__buffer {
	background-color: #ffe5e5 !important; /* Светлый оттенок для буфера */
}

.custom-progress-bar .v-progress-linear__determinate {
	background-color: #ff0000 !important; /* Яркий красный */
	opacity: 1 !important; /* Убираем прозрачность */
}

.response-container {
	padding: 16px;
	border: 1px solid #e0e0e0;
	border-radius: 8px;
	background-color: #f9f9f9;
}

.response-container .question {
	font-size: 1rem;
	font-weight: bold;
	color: #2c3e50;
	margin-bottom: 8px;
}

.response-container .response {
	font-size: 1rem;
	line-height: 1.5;
	color: #34495e;
	margin-bottom: 12px;
}

.response-container .technical-message {
	font-size: 0.875rem;
	color: #8e8e8e;
	font-style: italic;
}

.subscription-message {
	font-size: 1.1rem; /* Увеличенный шрифт для акцента */
	color: #2c3e50; /* Тёмный оттенок для текста */
	text-align: center; /* Центрируем текст */
	margin: 20px 0; /* Добавляем отступы сверху и снизу */
	padding: 10px 15px; /* Внутренний отступ */
	background-color: #f9f9f9; /* Светлый фон для выделения */
	border: 1px solid #e0e0e0; /* Лёгкая рамка */
	border-radius: 8px; /* Скругленные углы */
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Лёгкая тень */
}

.response-percentage {
	display: flex;
	align-items: center;
	font-size: 1rem;
	color: #34495e;
	background-color: #f4f6f8;
	border: 1px solid #dcdde1;
	padding: 15px 20px;
	border-radius: 8px;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	margin: 20px 0;
}

.response-icon {
	margin-right: 10px;
	font-size: 1.5rem;
}

.scroll-link {
	color: #3498db;
	text-decoration: underline;
	font-weight: bold;
	cursor: pointer;
	transition: color 0.3s ease;
}

.scroll-link:hover {
	color: #2980b9;
	text-decoration: none;
}

.subscription-header {
	font-size: 1.2rem;
	color: #2c3e50;
	text-align: center;
	margin: 30px 0;
	font-weight: bold;
}

.subscription-card {
	border: 1px solid #dcdde1;
	border-radius: 12px;
	background-color: #ffffff;
	transition: transform 0.3s, box-shadow 0.3s;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.subscription-card:hover {
	transform: translateY(-5px);
	box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.subscription-icon {
	font-size: 2.5rem;
	margin-bottom: 10px;
}

.subscription-price {
	font-size: 1.5rem;
	color: #2c3e50;
	font-weight: bold;
	margin: 10px 0;
}

.subscription-details {
	color: #7f8c8d;
	font-size: 1rem;
	margin-bottom: 20px;
}

.subscription-btn {
	background-color: #3498db;
	color: white;
	font-weight: bold;
	border-radius: 8px;
	transition: background-color 0.3s;
}

.subscription-btn:hover {
	background-color: #2980b9;
}

.question {
	font-size: 1.2rem;
	font-weight: bold;
	color: #2c3e50;
	margin-bottom: 10px;
}

.question-label {
	color: #8e44ad; /* Фиолетовый для выделения "Question:" */
	font-weight: bold;
	margin-right: 5px;
}

.response {
	font-size: 1rem;
	line-height: 1.6;
	color: #34495e;
	margin-bottom: 20px;
}

.response-label {
	color: #2980b9; /* Синий для выделения "Response:" */
	font-weight: bold;
	margin-right: 5px;
}

.read-more {
	color: #7f8c8d;
	font-style: italic;
	font-size: 0.9rem;
}

.read-more-link {
	color: #3498db;
	text-decoration: underline;
	font-weight: bold;
	cursor: pointer;
	transition: color 0.3s ease;
}

.read-more-link:hover {
	color: #2980b9;
	text-decoration: none;
}

.header-container {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 10px 20px;
	background-color: #f8f9fa;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	position: sticky;
	top: 0;
	z-index: 1000;
}

.header-title {
	font-size: 1.5rem;
	font-weight: bold;
	color: #2c3e50;
	margin: 0;
}

.profile-header {
	display: flex;
	align-items: center;
	justify-content: flex-end;
}

.profile-details {
	display: flex;
	align-items: center; /* Выравнивание текста и иконки по центру */
	margin-left: 10px;
}

.user-name {
	font-weight: bold;
	color: #2c3e50;
	font-size: 0.9rem;
	margin-right: 5px; /* Отступ между именем и email */
}

.user-email {
	font-size: 0.9rem;
	color: #7f8c8d;
}

.login-btn {
	font-size: 0.9rem;
}

.v-tabs {
	border-bottom: 2px solid #e0e0e0;
}

.v-tabs .v-tab {
	font-weight: bold;
	color: #34495e;
	text-transform: uppercase;
}

.v-tabs .v-tab--active {
	color: #2c3e50;
	border-bottom: 2px solid #3498db;
}

</style>
