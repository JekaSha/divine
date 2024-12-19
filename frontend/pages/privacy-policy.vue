<template>

	<InfoBanner />

	<v-container class="py-8">
		<v-row justify="center">
			<v-col cols="12" md="8">
				<h1 class="text-h4 font-weight-bold mb-6">Privacy Policy</h1>
				<p class="text-body-2 text-gray">Last Updated: 12/17/24</p>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">1. Information We Collect</h2>
					<p>
						When you use our service, we collect:
					</p>
					<ul class="pl-4">
						<li>Your Email Address: Collected when you submit your query to send you personalized responses.</li>
						<li>Your Query Submission: The personal or spiritual challenge you share through our service.</li>
					</ul>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">2. How We Use Your Information</h2>
					<p>
						We use your information for the following purposes:
					</p>
					<ul class="pl-4">
						<li>To deliver your personalized response: Your email is used to send you tailored, faith-based guidance.</li>
						<li>To send occasional updates: You may receive occasional marketing emails, such as service updates or related faith-based content.</li>
						<li>To improve our services: Your submissions (anonymized) may help us improve the quality of the guidance provided.</li>
					</ul>
					<p>
						We do not sell or share your personal information with third parties for marketing purposes.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">3. Marketing Communications</h2>
					<p>
						By submitting your query, you agree to receive occasional emails from Divine Guide. These may include updates, promotions, or additional resources:
					</p>
					<ul class="pl-4">
						<li>You can unsubscribe at any time by clicking “Unsubscribe” at the bottom of any email.</li>
						<li>Once unsubscribed, you will no longer receive promotional emails but will still receive essential service-related messages, if any.</li>
					</ul>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">4. Data Security</h2>
					<p>
						We take reasonable measures to safeguard your information from unauthorized access, loss, or misuse. Your data is securely stored and protected.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">5. Your Rights</h2>
					<p>You have the right to:</p>
					<ul class="pl-4">
						<li>Access: Request what personal information we hold.</li>
						<li>Delete: Request that we remove your personal data from our systems.</li>
					</ul>
					<p>
						To make any requests or inquiries, please contact us through
						<a href="mailto:contact@divine.guide" class="text-primary text-decoration-underline">contact@divine.guide</a>
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">6. Data Retention</h2>
					<p>
						Your email address and query submissions are retained only as long as necessary to fulfill the purposes outlined above or until you request their deletion.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">7. Changes to This Policy</h2>
					<p>
						We may update this Privacy Policy periodically. Any changes will be posted on this page with the updated “Last Updated” date.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">8. Contact Us</h2>
					<p>
						For any questions or concerns about this Privacy Policy, please visit our website:
					</p>
					<p>
						🌐
						<a href="https://divine.guide" target="_blank" class="text-primary text-decoration-underline">divine.guide</a>
					</p>
				</section>

				<p class="mt-12 font-weight-bold text-center">
					Thank you for trusting Divine Guide. We value your privacy and are committed to protecting your information.
				</p>
			</v-col>
		</v-row>
	</v-container>

	<Footer />

</template>

<script setup>
</script>

<style scoped>
ul {
	list-style-type: disc;
	margin-left: 1.5rem;
	color: #555;
}

p {
	line-height: 1.6;
	color: #333;
}

h1, h2 {
	color: #2c3e50;
}

a {
	color: #8e44ad;
}
</style>
