<template>
    <div class="row">
        <div class="col-md-3">
            <div class="dataTables_length" id="dataTable_length">
                <label>
                    Show
                    <select name="dataTable_length" v-model="per_page" aria-controls="dataTable" class="custom-select custom-select-sm form-control form-control-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </label>
            </div>
        </div>
        <div class="col-md-9">
            <div id="dataTable_filter" class="dataTables_filter row">
                <slot></slot>
                <div class="col-1">
                    <button @click="search()" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "listFilter",
        methods : {
            search : function () {
                this.$emit('click');
            }
        }
    }
</script>

<style scoped>

</style>