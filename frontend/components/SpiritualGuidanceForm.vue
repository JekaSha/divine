<template>
	<v-container class="py-12">
		<v-row justify="center" class="mb-1">
			<v-col cols="12" md="8" class="text-center">
				<h1 class="text-h4 font-weight-bold">{{ $t("index_title") }}</h1>
				<p class="text-body-1 mt" v-html="$t('index_description')"></p>
			</v-col>
		</v-row>

		<v-row justify="center">
			<v-col
				cols="12"
				md="6"
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

				<v-card class="pa-6" elevation="3">
					<!-- Loader or Error Message -->

					<v-card-title v-if="step === 1" class="text-h5 font-weight-bold">
						{{ $t('step_1_caption') }}
					</v-card-title>
					<v-card-title v-if="step === 2" class="text-h5 font-weight-bold">
						✝️ Enter Your Email
					</v-card-title>
					<v-card-title v-if="step === 3" class="text-h5 font-weight-bold">
						✝️ Thank You! Email Sent
					</v-card-title>
					<v-card-title v-if="step === 4" class="text-h5 font-weight-bold">
						✝️ Choose Your Plan
					</v-card-title>

					<!-- Step 1 -->
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

					<!-- Step 2 -->
					<v-form v-if="step === 2">
						<v-text-field
							label="Email"
							required
							outlined
							type="email"
							v-model="email"
						></v-text-field>
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

					<!-- Step 3 -->
					<div v-if="step === 3">
						<p>Your response has been sent to your email!</p>
						<v-btn
							color="primary"
							class="mt-6"
							block
							@click="showFullResponse"
						>
							Show Full Response
						</v-btn>
					</div>

					<!-- Step 4 -->
					<div v-if="step === 4">
						<p>Choose the plan that works for you:</p>
						<v-row>
							<v-col cols="12" md="4">
								<v-card class="pa-4 text-center" outlined>
									<p>$9/month</p>
									<p>50 requests per month</p>
									<v-btn color="primary" block @click="choosePlan(9)">
										Choose
									</v-btn>
								</v-card>
							</v-col>
							<v-col cols="12" md="4">
								<v-card class="pa-4 text-center" outlined>
									<p>$14/month</p>
									<p>200 requests per month</p>
									<v-btn color="primary" block @click="choosePlan(14)">
										Choose
									</v-btn>
								</v-card>
							</v-col>
							<v-col cols="12" md="4">
								<v-card class="pa-4 text-center" outlined>
									<p>$26/month</p>
									<p>1000 requests per month</p>
									<v-btn color="primary" block @click="choosePlan(26)">
										Choose
									</v-btn>
								</v-card>
							</v-col>
						</v-row>
					</div>
				</v-card>
			</v-col>
		</v-row>
	</v-container>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";

const step = ref(1);
const challenge = ref("");
const email = ref("");
const isLoading = ref(false);
const errorMessage = ref(null); // Состояние для хранения сообщения об ошибке
const userId = ref(generateUserId());

async function submitChallenge() {
	try {
		isLoading.value = true;
		errorMessage.value = null; // Сбрасываем ошибку
		const response = await axios.post("/api/challenge", {
			userId: userId.value,
			challenge: challenge.value,
		});
		if (response.status === 200) {
			step.value = 2; // Переход на шаг 2
		} else {
			throw new Error(response.data.message || "Unknown error");
		}
	} catch (error) {
		errorMessage.value = error.response?.data?.message || error.message;
	} finally {
		isLoading.value = false;
	}
}

async function submitEmail() {
	try {
		isLoading.value = true;
		errorMessage.value = null; // Сбрасываем ошибку
		const response = await axios.post("/api/send-email", {
			userId: userId.value,
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

function clearError() {
	errorMessage.value = null; // Сброс ошибки
}

function showFullResponse() {
	step.value = 4; // Переход на шаг 4
}

function choosePlan(price) {
	alert(`Вы выбрали план за $${price}/месяц!`);
}

function generateUserId() {
	return (
		Math.random().toString(36).substr(2, 9) + "-" + new Date().getTime()
	);
}
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

</style>
