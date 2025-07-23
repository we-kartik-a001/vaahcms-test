<script setup>
import axios from 'axios';
import { onMounted, ref } from "vue";
import { useProductStore } from '../../stores/store-products';
import VhField from './../../vaahvue/vue-three/primeflex/VhField.vue';
import { useRoute } from 'vue-router';

const baseUrl = window.location.origin + '/vaahcms/vaahcms/public';
const store = useProductStore();
const route = useRoute();

const form_menu = ref();
const toggleFormMenu = (event) => form_menu.value.toggle(event);

const fileUploadHeaders = ref({});

onMounted(async () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        fileUploadHeaders.value = {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        };
    }

    if ((!store.item || Object.keys(store.item).length < 1) && route.params?.id) {
        await store.getItem(route.params.id);
    }

    await store.getFormMenu();
});

function uploadFiles(event) {
    const files = event.files;

    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);

        axios.post(store.url, formData, {
            headers: {
                ...fileUploadHeaders.value,
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            const path = response.data?.file_url;
            if (path) {
                store.selectedImages ||= [];
                store.selectedImages.push(path);
                store.item.images ||= [];
                store.item.images.push(path);
            }
        })
        .catch(error => {
            console.error('Upload failed:', error.response?.data || error);
        });
    }
}

async function removeImage(index) {
    const img = store.item.images[index];
    const imageUrl = getImageUrl(img);

    try {
        await axios.delete(store.deleteurl, {
            data: { path: imageUrl }, // send path as request body
            headers: fileUploadHeaders.value,
        });

        // Remove from UI only if server deletion is successful
        store.item.images.splice(index, 1);
        store.selectedImages?.splice(index, 1);

    } catch (error) {
        console.error('Error deleting image from server:', error.response?.data || error);
    }
}

const getImageUrl = (img) => {
    if (!img) return '';
    if (typeof img === 'string') {
        return img.startsWith('/') ? baseUrl + img : img;
    }
    if (img.product_image) {
        return img.product_image.startsWith('/') ? baseUrl + img.product_image : img.product_image;
    }
    return '';
};6
</script>

<template>
    <div class="col-6">
        <Panel class="is-small">
            <template #header>
                <div class="flex flex-row">
                    <div class="p-panel-title">
                        <span v-if="store.item && store.item.id">Update</span>
                        <span v-else>Create</span>
                    </div>
                </div>
            </template>

            <template #icons>
                <div class="p-inputgroup">
                    <Button class="p-button-sm" v-tooltip.left="'View'" v-if="store.item?.id" @click="store.toView(store.item)" icon="pi pi-eye" />
                    <Button label="Save" class="p-button-sm" v-if="store.item?.id" @click="store.itemAction('save')" icon="pi pi-save" />
                    <Button label="Create & New" v-else @click="store.itemAction('create-and-new')" class="p-button-sm" icon="pi pi-save" />
                    <Button type="button" @click="toggleFormMenu" class="p-button-sm" icon="pi pi-angle-down" aria-haspopup="true" />
                    <Menu ref="form_menu" :model="store.form_menu_list" :popup="true" />
                    <Button class="p-button-primary p-button-sm" icon="pi pi-times" @click="store.toList()" />
                </div>
            </template>

            <div v-if="store.item" class="mt-2">
                <VhField label="Name">
                    <InputText class="w-full" placeholder="Enter the name" v-model="store.item.name" required />
                </VhField>

                <VhField>
                    <Editor v-model="store.item.description" editorStyle="height: 320px" />
                </VhField>

                <VhField label="Stock">
                    <InputText class="w-full" placeholder="Enter the stock" v-model="store.item.stock" required />
                </VhField>

                <VhField label="Price">
                    <InputNumber v-model="store.item.price" placeholder="Enter the price" class="w-full" mode="decimal" required />
                </VhField>

                <VhField label="Upload Images">
                    <FileUpload name="file" :auto="false" :multiple="true" accept="image/*" customUpload @uploader="uploadFiles" />
                </VhField>

                <VhField label="Uploaded Images">
                    <div class="flex flex-wrap gap-3 mt-3" v-if="store.item.images?.length">
                        <div class="relative" v-for="(img, index) in store.item.images" :key="index">
                            <Image :src="getImageUrl(img)" alt="Uploaded image" width="100" height="100" class="shadow-2 rounded-md object-contain" preview />
                            <Button icon="pi pi-times" class="absolute top-0 right-0 p-button-danger p-button-sm" @click="removeImage(index)" />
                        </div>
                    </div>
                </VhField>

                <VhField label="Slug">
                    <InputText class="w-full" placeholder="Enter the slug" v-model="store.item.slug" required />
                </VhField>

                <VhField label="Is Active">
                    <InputSwitch v-model="store.item.is_active" :false-value="0" :true-value="1" class="p-inputswitch-sm" />
                </VhField>
            </div>
        </Panel>
    </div>
</template>