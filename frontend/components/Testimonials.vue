<template>
	<v-container>
		<v-row justify="center" class="text-center mb-6">
			<v-col cols="12" md="8">
				<h2 class="font-weight-bold">Testimonials</h2>
			</v-col>
		</v-row>
		<v-row justify="center">
			<v-col cols="12" md="8">
				<div v-for="(testimonial, index) in testimonials" :key="index" class="mb-6">
					<p>
						✨ <strong>{{ testimonial.location }}:</strong>
						<em>{{ testimonial.text }}</em>
					</p>
				</div>
			</v-col>
		</v-row>
	</v-container>
</template>

<script setup>
const testimonials = [
	{ location: "From Flagstaff, AZ", text: "I was at a crossroads in my life and unsure which direction to take. The guidance I received was deeply rooted in scripture..." },
	{ location: "From Boise, ID", text: "The response was exactly what I needed during a difficult time. It felt as if God Himself was speaking to me..." },
	{ location: "From Malmo, Sweden", text: "This service was a blessing in my life. The guidance I received was thoughtful, scripture-based, and so practical..." }
];
</script>

<style scoped>
p {
	font-size: 1rem;
	color: #333;
	line-height: 1.6;
}
</style>
