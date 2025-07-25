<script setup>
import {onMounted, ref, watch} from "vue";
import { useBlogStore } from '../../stores/store-blogs'

import VhField from './../../vaahvue/vue-three/primeflex/VhField.vue'
import {useRoute} from 'vue-router';


const store = useBlogStore();
const route = useRoute();

onMounted(async () => {

    /**
     * Fetch the record from the database
     */
    if((!store.item || Object.keys(store.item).length < 1)
            && route.params && route.params.id)
    {
        await store.getItem(route.params.id);
    }

    await store.getFormMenu();
});

// Watch for tags data and set tag_ids when available
watch(
    () => store.item && store.item.tags,
    (tags) => {
        if (Array.isArray(tags)) {
            store.item.tag_ids = tags.map(tag => tag.id);
        }
    },
    { immediate: true }
);


//--------form_menu
const form_menu = ref();
const toggleFormMenu = (event) => {
    form_menu.value.toggle(event);
};
//--------/form_menu

</script>
<template>

    <div class="col-6" >

        <Panel class="is-small">

            <template class="p-1" #header>


                <div class="flex flex-row">
                    <div class="p-panel-title">
                        <span v-if="store.item && store.item.id">
                            Update
                        </span>
                        <span v-else>
                            Create
                        </span>
                    </div>

                </div>


            </template>

            <template #icons>


                <div class="p-inputgroup">

                    <Button class="p-button-sm"
                            v-tooltip.left="'View'"
                            v-if="store.item && store.item.id"
                            data-testid="blogs-view_item"
                            @click="store.toView(store.item)"
                            icon="pi pi-eye"/>

                    <Button label="Save"
                            class="p-button-sm"
                            v-if="store.item && store.item.id"
                            data-testid="blogs-save"
                            @click="store.itemAction('save')"
                            icon="pi pi-save"/>

                    <Button label="Create & New"
                            v-else
                            @click="store.itemAction('create-and-new')"
                            class="p-button-sm"
                            data-testid="blogs-create-and-new"
                            icon="pi pi-save"/>


                    <!--form_menu-->
                    <Button
                        type="button"
                        @click="toggleFormMenu"
                        class="p-button-sm"
                        data-testid="blogs-form-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                    <Menu ref="form_menu"
                          :model="store.form_menu_list"
                          :popup="true" />
                    <!--/form_menu-->


                    <Button class="p-button-primary p-button-sm"
                            icon="pi pi-times"
                            data-testid="blogs-to-list"
                            @click="store.toList()">
                    </Button>
                </div>



            </template>


            <div v-if="store.item" class="mt-2">

                <Message severity="error"
                         class="p-container-message mb-3"
                         :closable="false"
                         icon="pi pi-trash"
                         v-if="store.item.deleted_at">

                    <div class="flex align-items-center justify-content-between">

                        <div class="">
                            Deleted {{store.item.deleted_at}}
                        </div>

                        <div class="ml-3">
                            <Button label="Restore"
                                    class="p-button-sm"
                                    data-testid="articles-item-restore"
                                    @click="store.itemAction('restore')">
                            </Button>
                        </div>

                    </div>

                </Message>


              <!-- Form:Start -->
                <VhField label="Name">
                    <div class="p-inputgroup">
                        <InputText class="w-full"
                                   placeholder="Enter the name"
                                   name="blogs-name"
                                   data-testid="blogs-name"
                                   @update:modelValue="store.watchItem"
                                   v-model="store.item.name" required/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <VhField label="Slug">
                    <div class="p-inputgroup">
                        <InputText class="w-full"
                                   placeholder="Enter the slug"
                                   name="blogs-slug"
                                   data-testid="blogs-slug"
                                   v-model="store.item.slug" required/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <VhField label="Description">
                    <div class="p-inputgroup">
                        <InputText class="w-full" 
                                   placeholder="Enter the Description" 
                                   name="blogs-description"
                                   data-testid="blogs-description" 
                                   v-model="store.item.description" 
                                   type="description" required />
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <VhField label="Excerpt">
                    <div class="p-inputgroup">
                        <InputText class="w-full" 
                                   placeholder="Enter the Excerpt" 
                                   name="blogs-excerpt"
                                   data-testid="blogs-excerpt" 
                                   v-model="store.item.excerpt" 
                                   type="excerpt" required />
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <VhField label="Status">
                    <Dropdown v-model="store.item.status_id" 
                              :options="store.assets.status || []" 
                              optionLabel="name"
                              optionValue="id" 
                              placeholder="Select a Status" 
                              class="w-full" />
                </VhField>

               
                <VhField label="Category">
                    <Dropdown v-model="store.item.category_id" 
                              :options="store.assets.categories || []" 
                              optionLabel="name"
                              optionValue="id" 
                              placeholder="Select a Category" 
                              class="w-full" />
                </VhField>

                <VhField label="Tags">
                    <MultiSelect
                        v-model="store.item.tag_ids"
                        :options="store.assets.tags || []"
                        optionLabel="name"
                        optionValue="id"
                        filter
                        placeholder="Select Tags"
                        :maxSelectedLabels="3"
                        class="w-full md:w-80"
                    />
                </VhField>

                <!--SEO Fields -->
                <VhField label="SEO Title" v-if="store.item.seo">
                    <InputText v-model="store.item.seo.seo_title"
                            placeholder="Enter the SEO Title"
                            class="w-full"
                            required />
                </VhField>

                <VhField label="SEO Description" v-if="store.item.seo">
                    <Textarea v-model="store.item.seo.seo_description"
                            placeholder="Enter the SEO Description"
                            class="w-full"
                            required />
                </VhField>

                <VhField label="SEO Metatags" v-if="store.item.seo">
                    <Chips v-model="store.item.seo.seo_metatag"
                        class="w-full"
                        required />
                </VhField>
                <!-- /SEO Fields -->

              <!-- Form:End -->

                <VhField label="Is Active">
                    <InputSwitch v-bind:false-value="0"
                                 v-bind:true-value="1"
                                 class="p-inputswitch-sm"
                                 name="blogs-active"
                                 data-testid="blogs-active"
                                 v-model="store.item.is_active"/>
                </VhField>

            </div>
        </Panel>

    </div>

</template>
