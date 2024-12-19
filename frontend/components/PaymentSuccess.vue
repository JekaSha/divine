<template>

	<v-row justify="center" class="text-center my-4" v-if="showPaymentBanner">
		<v-col cols="12" md="8">
			<div class="payment-banner">
				<v-icon color="green" class="success-icon">mdi-check-circle</v-icon>
				<p class="text-h6 font-weight-bold text-success mb-2">
					Payment Received Successfully!
				</p>
				<p class="text-body-2">
					Thank you for your payment. Your access has been updated.
				</p>

				<v-btn color="primary" small elevation="1" @click="showPaymentBanner = false">
					Close
				</v-btn>
			</div>
		</v-col>
	</v-row>


</template>
<script setup>
const showPaymentBanner = ref(true);


</script>
<style>

.info-banner {
	padding: 32px;
	background-color: #f9f9f9;
	border: 1px solid #e0e0e0;
	border-radius: 12px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.info-icon {
	font-size: 3rem;
}

.payment-banner {
	padding: 16px;
	background-color: #e9f7e9;
	border: 1px solid #d4edda;
	border-radius: 12px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	animation: fade-in 0.5s ease-in-out;
}

.success-icon {
	font-size: 2.5rem;
	margin-bottom: 8px;
}

.text-success {
	color: #28a745;
}

@keyframes fade-in {
	from {
		opacity: 0;
		transform: translateY(-10px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

h1 {
	color: #2c3e50;
}

p {
	color: #555;
	line-height: 1.6;
}

v-btn {
	font-weight: bold;
}

</style>