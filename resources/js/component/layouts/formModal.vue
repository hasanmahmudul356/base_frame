<template>
    <div class="modal fade" :id="modalId">
        <div :class="`modal-dialog ${modalSize}`">
            <div class="modal-content">
                <form @submit.prevent="submit()">
                    <div class="modal-header">
                        <h4 class="modal-title">{{modalTitle}}</h4>
                        <button type="button" class="close" @click="closeModal(modalId)">&times;</button>
                    </div>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "formModal",
        props: {
            modalId: {
                type: [String],
                default: 'formModal'
            },
            modalSize: {
                type: [String],
                default: 'modal-xl'
            }

        },
        methods : {
            submit : function () {
                this.$emit('submit');
            }
        }
    }
</script>

<style scoped>
    @media screen and (max-width: 767px) {
        .text-right {
            text-align: left !important;
        }
    }
</style>