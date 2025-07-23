<script setup>
import { ref } from 'vue';
import { useorderStore } from '../../../stores/store-orders';
import VhFieldVertical from './../../../vaahvue/vue-three/primeflex/VhFieldVertical.vue';

const store = useorderStore();

// Make sure max_price is a number
const maxPrice = Number(store.assets.max_price) || 0;

// Price range state
const price_range = ref([0, maxPrice]);

// Update filter values directly without watch
store.query.filter.price_min = price_range.value[0];
store.query.filter.price_max = price_range.value[1];

function onPriceChange() {
    store.query.filter.price_min = price_range.value[0];
    store.query.filter.price_max = price_range.value[1];
}

</script>

<template>
    <div class="col-3" v-if="store.show_advance_filter">

        <Panel class="is-small">

            <template class="p-1" #header>

                <div class="flex flex-row">
                    <div>
                        <b class="mr-1">Advance Filters</b>
                    </div>

                </div>

            </template>

            <template #icons>

                <div class="p-inputgroup">

                    <Button data-testid="orders-hide-filter" class="p-button-sm"
                        @click="store.show_advance_filter = false">
                        <i class="pi pi-times"></i>
                    </Button>

                </div>

            </template>

            <VhFieldVertical>

                <p class="font-semibold">Filter Status</p>
                <Dropdown v-model="store.query.filter.status_id" :options="store.assets.status || []" optionLabel="name"
                    optionValue="id" placeholder="Select a Status" class="w-full" />

            </VhFieldVertical>

            <VhFieldVertical>
                <p class="font-semibold">Price Range</p>
                <!-- Optional: Label Underneath -->
                <div class="w-full text-xs text-gray-500 py-1">
                    Slide to filter orders by total price
                </div>

                <!-- Slider -->
                <div class="p-3">
                    <Slider v-model="price_range" range :min="0" :max="maxPrice" :step="500" class="w-full"
                        @change="onPriceChange" />
                </div>
                <!-- Price Range Display -->
                <div class="w-full text-center text-sm text-gray-700 font-medium py-1 space-y-1">
                    <div>
                        min: <span class="text-green-500">₹ {{ price_range[0].toLocaleString() }}</span>
                    </div>
                    <div>
                        max: <span class="text-green-500">₹ {{ price_range[1].toLocaleString() }}</span>
                    </div>
                </div>
            </VhFieldVertical>

        </Panel>

    </div>
</template>
