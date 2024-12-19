<template>
	<v-footer color="grey lighten-4" class="footer-container">
		<v-container>
			<v-row justify="center" align="center" class="text-center">
				<v-col cols="12">
					<div class="footer-content">
						<v-icon color="purple" class="footer-icon">mdi-church</v-icon>
						<p class="footer-text">
							Links for legal information:
							<NuxtLink :to="localePath('/terms-of-service')" class="footer-link">Terms of Service</NuxtLink> |
							<NuxtLink :to="localePath('/privacy-policy')" class="footer-link">Privacy Policy</NuxtLink>
						</p>
					</div>
				</v-col>
			</v-row>
		</v-container>
	</v-footer>
</template>

<script setup></script>

<style scoped>
.footer-container {
	padding: 16px 0;
	background-color: #f9f9f9;
	border-top: 2px solid #e0e0e0;
}

.footer-content {
	display: flex;
	justify-content: center;
	align-items: center;
}

.footer-icon {
	font-size: 1.5rem;
	margin-right: 8px;
}

.footer-text {
	color: #333;
	font-size: 1rem;
	margin: 0;
}

.footer-link {
	color: #8e44ad; /* Фиолетовый цвет, соответствующий сайту */
	text-decoration: none;
	font-weight: bold;
}

.footer-link:hover {
	text-decoration: underline;
	color: #6f1e83; /* Темнее при наведении */
}
</style>
