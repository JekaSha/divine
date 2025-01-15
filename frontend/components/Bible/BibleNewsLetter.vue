<script setup lang="ts">
import { ref } from 'vue';


const email = ref('');

const handleSubmit = (e: Event) => {
	e.preventDefault();
	//toast.success("Thanks for subscribing!");
	email.value = '';
};
</script>

<template>
	<div class="w-full max-w-xl mx-auto text-center">
		<h2 class="text-2xl font-medium mb-8">Sign up to receive email updates</h2>
		<form @submit="handleSubmit" class="newsletter-container">
			<input
				type="email"
				v-model="email"
				placeholder="Enter your email..."
				class="newsletter-input"
				required
			/> 
			<BibleButton type="submit" class="newsletter-button">
				Subscribe
			</BibleButton>
		</form>
	</div>
</template>
