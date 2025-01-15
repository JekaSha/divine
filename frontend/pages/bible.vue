<script setup lang="ts">
import "@/assets/css/bible/bible.css";


const handleSearch = (query: string) => {
	console.log('Search:', query);
};
</script>

<template>
	<div class="min-h-screen py-12 px-4 space-y-12">
		<div class="min-h-screen">
			<!-- Existing header section -->
			<div class="py-12 px-4 space-y-12">
				<header class="text-center space-y-4">
					<BibleLogo />
					<h1 class="text-2xl md:text-3xl max-w-2xl mx-auto">
						Bible Ai - Millions of answers backed by biblical knowledge
					</h1>
				</header>

				<!-- Search Section -->
				<section>
					<BibleSearchInput @submit="handleSearch" />
				</section>

				<!-- Example Cards -->
				<section class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
					<BibleExampleCard
						title="Describe your situation for advice"
						:examples="[
            'My best friend Anna and I had a fight and I don\'t know how to deal with it'
          ]"
					/>
					<BibleExampleCard
						title="Ask Bible Ai any facts about the Bible"
						:examples="[
            'What is the book of Matthew?',
            'Where did Amos live?'
          ]"
					/>
				</section>

				<!-- Newsletter -->
				<section>
					<BibleNewsletter />
				</section>

				<!-- Footer Navigation -->
				<footer class="flex justify-center gap-4">
					<BibleButton
						variant="secondary"
						class="rounded-full bg-[#2a2d3d] hover:bg-[#363a4f]"
					>
						About
					</BibleButton>
				</footer>
			</div>
		</div>
	</div>
</template>
