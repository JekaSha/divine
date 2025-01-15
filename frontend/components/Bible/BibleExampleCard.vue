<script setup lang="ts">
import { defineProps } from 'vue';

interface ExampleCardProps {
	title: string;
	examples: string[];
}

defineProps<ExampleCardProps>();
</script>

<template>
	<div class="card-gradient rounded-2xl p-6 backdrop-blur-sm">
		<h3 class="text-xl font-semibold mb-4">{{ title }}</h3>
		<div class="space-y-3">
			<p
				v-for="(example, index) in examples"
				:key="index"
				class="text-muted-foreground"
			>
				"{{ example }}"
			</p>
		</div>
	</div>
</template>
