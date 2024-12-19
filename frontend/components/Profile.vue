<template>
	<v-container class="header-container py-2">
		<!-- Верхний ряд: Заголовок и профиль -->
		<v-row align="center" justify="space-between">
			<div class="profile-header">
				<!-- Аватар и информация пользователя -->
				<v-avatar v-if="user && user.id" size="50" class="mr-4">
					<v-icon size="40">mdi-account-circle</v-icon>
				</v-avatar>

				<div v-if="user && user.id" class="profile-info">
					<v-card flat class="user-details-card pa-2">
						<div class="user-details">
							<span class="user-name">{{ user.name || "Guest" }}</span>
							<span class="user-email">{{ user.email }}</span>
						</div>
					</v-card>
				</div>
			</div>

			<!-- Информация о доступах -->
			<v-card flat class="access-info-card pa-2" v-if="user && user.id">
				<v-row no-gutters>
					<v-col cols="6" class="text-center">
						<div class="info-block">
							<span class="info-label">Remaining Days:</span>
							<span class="info-value">{{ remainingDays }}</span>
						</div>
					</v-col>
					<v-col cols="6" class="text-center">
						<div class="info-block">
							<span class="info-label">Requests:</span>
							<span class="info-value">{{ remainingRequests }}</span>
						</div>
					</v-col>
				</v-row>
			</v-card>

			<!-- Кнопка входа -->
			<!--<v-btn v-else color="primary" @click="redirectToLogin" class="login-btn">
				Login
			</v-btn>-->
		</v-row>
	</v-container>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
	user: {
		type: Object,
		required: true,
	},
	permissions: {
		type: Object,
		required: true,
	},
});

const remainingDays = computed(() => {
	if (!props.permissions?.expired_date) return 0;
	const now = Math.floor(Date.now() / 1000);
	const remainingSeconds = props.permissions.expired_date - now;
	return Math.max(Math.ceil(remainingSeconds / (24 * 3600)), 0);
});

const remainingRequests = computed(() => props.permissions?.requests ?? 0);

const redirectToLogin = () => {
	window.location.href = '/login';
};
</script>

<style scoped>
.header-container {
	background-color: #f5f5f5;
	border-radius: 8px;
	box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
	padding: 8px 16px; /* Уменьшаем вертикальные отступы */
}
.v-card {
	padding: 4px 8px; /* Уменьшаем внутренние отступы карточек */
}

.profile-header {
	display: flex;
	align-items: center;
	min-height: 50px; /* Можно задать минимальную высоту */
}

.profile-info .user-details-card {
	background-color: transparent;
	box-shadow: none;
}

.user-details {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
}

.user-name {
	font-size: 1.2rem;
	font-weight: bold;
	color: #333;
}

.user-email {
	font-size: 0.9rem;
	color: #555;
}

.access-info-card {
	background-color: #e8f5e9; /* Светло-зеленый фон */
	border-radius: 8px;
}

.info-block {
	margin-top: 4px;
	margin-bottom: 4px;
}

.info-label {
	font-size: 0.9rem;
	color: #777;
}

.info-value {
	font-size: 1.3rem;
	font-weight: bold;
	color: #4caf50;
}

.login-btn {
	font-weight: bold;
}
</style>
