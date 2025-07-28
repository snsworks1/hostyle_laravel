<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="bg-white/5 text-white placeholder-white/60 border border-white/30 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm transition-all"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
