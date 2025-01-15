<script setup lang="ts">
import { ref, defineProps } from 'vue';

interface SearchInputProps {
	onSubmit: (query: string) => void;
}

defineProps<SearchInputProps>();

const query = ref('');

const handleSubmit = (e: Event) => {
	e.preventDefault();
	onSubmit(query.value);
};
</script>

<template>
	<form @submit="handleSubmit" class="search-container">
		<input
			type="text"
			v-model="query"
			placeholder="Describe your situation or ask me a question..."
			class="search-input"
		/>
		<BibleButton type="submit" class="search-button">
			Ask
		</BibleButton>
	</form>
</template>
