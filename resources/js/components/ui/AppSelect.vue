<script setup lang="ts">
import {
    SelectContent,
    SelectGroup,
    SelectIcon,
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    SelectPortal,
    SelectRoot,
    SelectTrigger,
    SelectValue,
    SelectViewport,
} from 'radix-vue';
import { ChevronDown, Check } from 'lucide-vue-next';

export interface SelectOption {
    value: string | number;
    label: string;
}

const props = defineProps<{
    modelValue?: string | number;
    options: SelectOption[];
    placeholder?: string;
    disabled?: boolean;
    class?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();
</script>

<template>
    <SelectRoot 
        :model-value="modelValue !== undefined && modelValue !== null && modelValue !== '' ? String(modelValue) : (options.some(o => o.value === '') ? '__empty__' : undefined)" 
        @update:model-value="val => emit('update:modelValue', val === '__empty__' ? '' : val)" 
        :disabled="disabled"
    >
        <SelectTrigger
            class="flex w-full min-h-[44px] md:min-h-10 items-center justify-between rounded-xl px-4 py-2.5 md:px-3 md:py-2 text-base md:text-sm ring-offset-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[placeholder]:text-on-surface-variant/50"
            :class="[
                {
                    'bg-surface': !props.class?.includes('bg-'),
                    'border border-outline-variant/50': !props.class?.includes('border'),
                    'text-on-surface': !props.class?.includes('text-'),
                    'h-auto': !props.class?.match(/\bh-/),
                },
                props.class
            ]"
        >
            <SelectValue :placeholder="placeholder" />
            <SelectIcon as-child>
                <ChevronDown class="h-4 w-4 opacity-50" />
            </SelectIcon>
        </SelectTrigger>

        <SelectPortal>
            <SelectContent
                class="relative z-50 min-w-32 overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container-high text-on-surface shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                position="popper"
                :side-offset="4"
            >
                <SelectViewport class="p-1">
                    <SelectGroup>
                        <SelectItem
                            v-for="option in options"
                            :key="option.value === '' ? '__empty__' : option.value"
                            :value="option.value === '' ? '__empty__' : String(option.value)"
                            class="relative flex w-full cursor-pointer select-none items-center rounded-lg py-2 pl-8 pr-2 text-sm outline-none focus:bg-primary/10 focus:text-primary data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
                        >
                            <span class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
                                <SelectItemIndicator>
                                    <Check class="h-4 w-4" />
                                </SelectItemIndicator>
                            </span>
                            <SelectItemText>{{ option.label }}</SelectItemText>
                        </SelectItem>
                    </SelectGroup>
                </SelectViewport>
            </SelectContent>
        </SelectPortal>
    </SelectRoot>
</template>
