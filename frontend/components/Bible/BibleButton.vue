<template>
	<component
		:is="asChild ? 'slot' : 'button'"
		:class="computedClass"
		v-bind="props"
		v-on="$listeners"
		ref="buttonRef"
	>
		<slot />
	</component>
</template>

<script>
import { computed, defineComponent } from "vue";

const buttonVariants = ({
							variant = "default",
							size = "default",
							className = "",
						} = {}) => {
	const baseClasses = [
		"inline-flex",
		"items-center",
		"justify-center",
		"gap-2",
		"whitespace-nowrap",
		"rounded-md",
		"text-sm",
		"font-medium",
		"ring-offset-background",
		"transition-colors",
		"focus-visible:outline-none",
		"focus-visible:ring-2",
		"focus-visible:ring-ring",
		"focus-visible:ring-offset-2",
		"disabled:pointer-events-none",
		"disabled:opacity-50",
		"[&_svg]:pointer-events-none",
		"[&_svg]:size-4",
		"[&_svg]:shrink-0",
	];

	const variantClasses = {
		default: "bg-primary text-primary-foreground hover:bg-primary/90",
		destructive: "bg-destructive text-destructive-foreground hover:bg-destructive/90",
		outline: "border border-input bg-background hover:bg-accent hover:text-accent-foreground",
		secondary: "bg-secondary text-secondary-foreground hover:bg-secondary/80",
		ghost: "hover:bg-accent hover:text-accent-foreground",
		link: "text-primary underline-offset-4 hover:underline",
	};

	const sizeClasses = {
		default: "h-10 px-4 py-2",
		sm: "h-9 rounded-md px-3",
		lg: "h-11 rounded-md px-8",
		icon: "h-10 w-10",
	};

	return [
		...baseClasses,
		variantClasses[variant],
		sizeClasses[size],
		className,
	].join(" ");
};

export default defineComponent({
	name: "Button",
	props: {
		asChild: {
			type: Boolean,
			default: false,
		},
		variant: {
			type: String,
			default: "default",
		},
		size: {
			type: String,
			default: "default",
		},
		className: {
			type: String,
			default: "",
		},
	},
	setup(props, { slots, attrs }) {
		const computedClass = computed(() => buttonVariants(props));

		return {
			computedClass,
			props: attrs,
			slots,
		};
	},
});
</script>

<style scoped>
/* Add any additional custom styles here */
</style>
