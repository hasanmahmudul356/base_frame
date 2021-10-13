<template>
    <div class="d-sm-flex justify-content-between mt-2 mb-3 row">
        <div class="col-6 text-left">
            <h1 class="h3 mb-0 text-gray-800">
                <span>{{pageHeading}}</span>
                <a v-if="httpRequest" style="margin-left: 10px">
                    <i class="fa fa-spin fa-spinner"></i>
                </a>
            </h1>
        </div>
        <div class="col-6 text-right">
            <button v-if="can('add')" @click="openModal(modalId, modalHeader)" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fa fa-plus fa-sm text-white-50"></i>{{buttonText}}
            </button>
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        name: "pageTop",
        props: {
            pageHeading: {
                type: String,
                default: 'Dashboard'
            },
            modalId: {
                type: String,
                default: 'formModal'
            },
            modalHeader: {
                type: String,
                default: 'Add New'
            },
            buttonText: {
                type: String,
                default: 'Add New'
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>