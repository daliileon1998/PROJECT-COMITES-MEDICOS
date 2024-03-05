<script setup>
import { defineProps, defineEmits, defineExpose, onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    options: {
        type: Array, // Asegúrate de que el tipo sea correcto
        required: true,
    },
});

const emits = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

</script>

<template>
    <select
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        ref="input"
    >
        <option v-for="op in options" :key="op.id" :value="op.id">{{ op.name }}</option>
    </select>
</template>

