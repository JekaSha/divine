<template>

	<InfoBanner />

	<v-container class="py-8">
		<v-row justify="center">
			<v-col cols="12" md="8">
				<h1 class="text-h4 font-weight-bold mb-6">Divine Guide Terms of Service</h1>
				<p class="text-body-2 text-gray">Last Updated: 12/18/24</p>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">1. Acceptance of Terms</h2>
					<p>
						By accessing or using Divine Guide, you agree to comply with these Terms and all applicable laws and regulations.
						You must be at least 18 years old to use our services.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">2. Services Provided</h2>
					<p>
						Divine Guide offers personalized, faith-based guidance grounded in Christian teachings. Responses are delivered to the
						email address provided upon submission of your request.
					</p>
					<ul class="pl-4">
						<li>All submissions are private and anonymous.</li>
						<li>Responses are generated using AI and are intended for spiritual reflection, support, and encouragement.</li>
						<li>
							We do not provide professional counseling, medical advice, or legal advice. If you need professional assistance,
							please consult a qualified professional.
						</li>
					</ul>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">3. User Responsibilities</h2>
					<p>When using our services, you agree to:</p>
					<ul class="pl-4">
						<li>Submit accurate and truthful information in your request.</li>
						<li>Use the guidance received responsibly and in alignment with your personal faith journey.</li>
						<li>Refrain from using our platform for unlawful purposes, spamming, or sharing inappropriate content.</li>
					</ul>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">4. Privacy</h2>
					<p>
						Your privacy is important to us. All submissions are treated with strict confidentiality. For more details, please review
						our <NuxtLink to="/privacy" class="text-decoration-underline text-primary font-weight-bold">Privacy Policy</NuxtLink>.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">5. Intellectual Property</h2>
					<p>
						All content provided through Divine Guide, including responses, website design, text, and graphics, is owned by Divine Guide
						or licensed to us. You may not copy, reproduce, or distribute our content without permission.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">6. Service Limitations</h2>
					<p>
						While we strive to provide high-quality responses, we cannot guarantee:
					</p>
					<ul class="pl-4">
						<li>That responses will meet your expectations or provide specific outcomes.</li>
						<li>Uninterrupted or error-free access to our website or services.</li>
					</ul>
					<p>
						Divine Guide reserves the right to modify, suspend, or discontinue our services at any time without notice.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">7. Limitation of Liability</h2>
					<p>
						To the maximum extent permitted by law:
					</p>
					<ul class="pl-4">
						<li>
							Divine Guide is not liable for any direct, indirect, or consequential damages resulting from your use of our services.
						</li>
						<li>Our guidance is for spiritual support only and is not a substitute for professional advice.</li>
					</ul>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">8. Refund Policy</h2>
					<p>
						Due to the personalized nature of our service, all sales are final, and refunds will not be issued. If you experience any issues,
						please contact us at <a href="mailto:contact@divine.guide" class="text-primary text-decoration-underline">contact@divine.guide</a>.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">9. Changes to Terms</h2>
					<p>
						We may update these Terms from time to time. Any changes will be posted on this page with an updated "Last Updated" date. Your
						continued use of the service constitutes acceptance of the new Terms.
					</p>
				</section>

				<section>
					<h2 class="text-h5 font-weight-bold mt-8">10. Contact Us</h2>
					<p>
						If you have any questions or concerns about these Terms, please contact us:
					</p>
					<ul class="pl-4">
						<li>Email: <a href="mailto:contact@divine.guide" class="text-primary text-decoration-underline">contact@divine.guide</a></li>
						<li>Website: <a href="https://divine.guide" target="_blank" class="text-primary text-decoration-underline">divine.guide</a></li>
					</ul>
				</section>

				<p class="mt-12 font-weight-bold text-center">
					Thank you for using Divine Guide. We are honored to support you on your faith journey.
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
