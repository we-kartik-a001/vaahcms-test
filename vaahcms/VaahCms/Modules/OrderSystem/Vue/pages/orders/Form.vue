<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { useorderStore } from '../../stores/store-orders'
import VhField from './../../vaahvue/vue-three/primeflex/VhField.vue'
import { useRoute } from 'vue-router';

const store = useorderStore();
const route = useRoute();

onMounted(async () => {
    if ((!store.item || Object.keys(store.item).length < 1)
        && route.params && route.params.id) {
        await store.getItem(route.params.id);
    }

    await store.getFormMenu();

    if (route.params && route.params.id) {
        await store.getItem(route.params.id);
        initializeSelectedProducts();
    }
});

async function initializeSelectedProducts() {
    if (store.item && Array.isArray(store.item.products)) {
        store.selectedProductIds = store.item.products.map(p => p.id)
        store.item.products.forEach((product) => {
            store.quantities[product.id] = product.pivot?.quantity || product.quantity || 1
        })
    }
}

const form_menu = ref();
const toggleFormMenu = (event) => {
    form_menu.value.toggle(event);
};

watch(() => route.params.id, async (newId) => {
    if (newId) {
        store.selectedProductIds = []
        store.quantities = {}
        await store.getItem(newId);
        initializeSelectedProducts();
    }
});

// Realtime quantity update handler
const updateQuantity = (productId, event) => {
    const value = Number(event.value || 0);
    store.quantities[productId] = value;
};

const productTotals = computed(() => {
    const totals = {};
    store.selectedProductIds.forEach((id) => {
        const product = store.assets.products.find(p => p.id === id);
        const qty = Number(store.quantities[id] || 0);
        const price = Number(product?.price || 0);
        totals[id] = qty * price;
    });
    return totals;
});

const grandTotal = computed(() => {
    return Object.values(productTotals.value).reduce((sum, val) => sum + val, 0);
});

const totalQuantity = computed(() => {
    return Object.values(store.quantities).reduce((sum, qty) => sum + Number(qty), 0);
});

const pivotData = computed(() => {
    return store.selectedProductIds.map(id => ({
        id: id,
        quantity: store.quantities[id] || 0
    }));
});

const prepareOrderBeforeSave = (action) => {
    store.item.total_price = grandTotal.value;
    store.item.total_quantity = totalQuantity.value;
    store.item.products = pivotData.value;

    if (action === 'create-and-new') {
        store.selectedProductIds = [];
        store.quantities = {};
    }
};
</script>
<template>
    <div class="col-6">
        <Panel class="is-small">
            <template class="p-1" #header>
                <div class="flex flex-row">
                    <div class="p-panel-title">
                        <span v-if="store.item && store.item.id">Update</span>
                        <span v-else>Create</span>
                    </div>
                </div>
            </template>

            <template #icons>
                <div class="p-inputgroup">
                    <Button class="p-button-sm" v-tooltip.left="'View'" v-if="store.item && store.item.id"
                        data-testid="orders-view_item" @click="store.toView(store.item)" icon="pi pi-eye" />
                    <Button label="Save" class="p-button-sm" v-if="store.item && store.item.id"
                        data-testid="orders-save" @click="prepareOrderBeforeSave(); store.itemAction('save')"
                        icon="pi pi-save" />
                    <Button label="Create & New"
                        @click="prepareOrderBeforeSave('create-and-new'); store.itemAction('create-and-new')"
                        class="p-button-sm" data-testid="orders-create-and-new" icon="pi pi-save" />
                    <Button type="button" @click="toggleFormMenu" class="p-button-sm" data-testid="orders-form-menu"
                        icon="pi pi-angle-down" aria-haspopup="true" />
                    <Menu ref="form_menu" :model="store.form_menu_list" :popup="true" />
                    <Button class="p-button-primary p-button-sm" icon="pi pi-times" data-testid="orders-to-list"
                        @click="store.toList()" />
                </div>
            </template>

            <div v-if="store.item" class="mt-2">
                <!-- Deleted Message -->
                <Message severity="error" class="p-container-message mb-3" :closable="false" icon="pi pi-trash"
                    v-if="store.item.deleted_at">
                    <div class="flex align-items-center justify-content-between">
                        <div>Deleted {{ store.item.deleted_at }}</div>
                        <div class="ml-3">
                            <Button label="Restore" class="p-button-sm" data-testid="orders-item-restore"
                                @click="store.itemAction('restore')" />
                        </div>
                    </div>
                </Message>

                <!-- Form Fields -->
                <VhField label="Name">
                    <InputText class="w-full" placeholder="Enter the name" name="orders-name" data-testid="orders-name"
                        @update:modelValue="store.watchItem" v-model="store.item.name" required />
                </VhField>

                <VhField label="Customer">
                    <Dropdown v-model="store.item.customer_id" :options="store.assets.customers || []"
                        optionLabel="name" optionValue="id" placeholder="Select Customer" class="w-full" />
                </VhField>

                <VhField label="Slug">
                    <InputText class="w-full" placeholder="Enter the slug" name="orders-slug" data-testid="orders-slug"
                        v-model="store.item.slug" required />
                </VhField>

                <VhField label="Products">
                    <MultiSelect v-model="store.selectedProductIds" :options="store.assets.products || []"
                        optionLabel="name" optionValue="id" filter placeholder="Select Products" :maxSelectedLabels="3"
                        class="w-full md:w-80" />
                </VhField>

                <VhField>
                    <!-- Product Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-100 text-left text-gray-700">
                                <tr>
                                    <th class="px-1 py-2 text-xs">Product</th>
                                    <th class="px-1 py-2 text-xs">Quantity</th>
                                    <th class="px-1 py-2 text-xs">Price (₹)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr v-for="productId in store.selectedProductIds" :key="productId">
                                    <td class="px-1 text-xs py-3 font-medium text-gray-800">
                                        {{store.assets.products.find(p => p.id === productId)?.name || '—'}}
                                    </td>

                                    <td class=" py-3">
                                        <InputNumber v-model="store.quantities[productId]" :min="0" showButtons
                                            buttonLayout="horizontal"  :inputStyle="{ textAlign: 'center' }"
                                            @input="updateQuantity(productId, $event)" />
                                        <div class="text-xs text-gray-500 mt-1">
                                            Stock: {{store.assets.products.find(p => p.id === productId)?.stock ?? 0}}
                                        </div>
                                        <div v-if="store.quantities[productId] > (store.assets.products.find(p => p.id === productId)?.stock ?? 0)"
                                            class="text-xs text-red-600 font-semibold mt-1">
                                            ⚠ Not enough stock available
                                        </div>
                                    </td>

                                    <td class="text-xs py-3 text-green-600 font-semibold ">
                                         {{ productTotals[productId]?.toLocaleString() || '0' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mt-4 flex flex-wrap justify-end gap-4 text-sm font-medium">
                        <div class="text-blue-600">
                            Total: ₹ {{ grandTotal.toLocaleString() }}
                        </div>
                        <div class="text-green-600">
                            Quantity: {{ totalQuantity }}
                        </div>
                    </div>
                </VhField>

                <VhField label="Status">
                    <Dropdown v-model="store.item.status_id" :options="store.assets.status || []" optionLabel="name"
                        optionValue="id" placeholder="Select a Status" class="w-full" />
                </VhField>

                <VhField label="Is Active">
                    <InputSwitch v-bind:false-value="0" v-bind:true-value="1" class="p-inputswitch-sm"
                        name="orders-active" data-testid="orders-active" v-model="store.item.is_active" />
                </VhField>
            </div>
        </Panel>
    </div>
</template>