<template>
	<v-container class="py-8">
		<v-row justify="center" align="center" class="text-center">
			<v-col cols="12" md="8">
				<div class="info-banner">
					<v-icon color="purple" class="info-icon mb-2">mdi-cross</v-icon>
					<h1 class="text-h4 font-weight-bold mb-4">
						Discover God's Answers, Tailored Just for You
					</h1>
					<p class="text-body-1 mb-6">
						Receive immediate, life-changing, personalized guidance. <br />
						<strong>Rooted in faith and scripture.</strong>
					</p>
					<RouterLink :to="localePath('/')" class="text-decoration-none">
						<v-btn color="primary" large elevation="2">
							Get My Personalized Guidance
						</v-btn>
					</RouterLink>
				</div>
			</v-col>
		</v-row>
	</v-container>
</template>

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