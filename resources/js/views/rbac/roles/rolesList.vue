<template>
    <div>
        <page-top page-heading="Role List" modal-header="Add new Role" modal-id="formModal" button-text="New Role"></page-top>
        <template>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <data-table :table-heading="tableHeading">
                        <template v-slot:filter>
                            <list-filter @click="getDataList()">
                                <div class="col-7"></div>
                                <div class="col-4">
                                    <input v-model="filter.keyword" type="search" class="form-control form-control-sm" placeholder="Search">
                                </div>
                            </list-filter>
                        </template>
                        <template v-slot:data>
                            <tr v-for="(data, index) in dataList.data" :key="index">
                                <td>{{index+1}}</td>
                                <td>{{data.name}}</td>
                                <td class="text-center">
                                    <button v-if="can('update')" class="btn btn-sm" @click="editData(data,data.id)">
                                        <i class="fa fa-edit text-warning"></i>
                                    </button>
                                    <button v-if="can('delete')" class="btn btn-sm" @click="deleteInformation(index, data.id)">
                                        <i class="fa fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </data-table>
                </div>
            </div>
        </template>
        <form-modal modal-id="formModal" modal-size="modal-md" @submit="submitForm(formObject,'formModal')">
            <div class="row">
                <div class="col-md-12 form-group">
                    <label>Name</label>
                    <input v-validate="'required'" name="name" type="text" v-model="formObject.name" class="form-control">
                </div>
            </div>
        </form-modal>
    </div>
</template>

<script>
    import PageTop from "../../../component/layouts/pageTop";
    import ListFilter from "../../../component/layouts/listFilter";
    import DataTable from "../../../component/layouts/dataTable";
    import FormModal from "../../../component/layouts/formModal";

    export default {
        name: "rolesList",
        components: {FormModal, DataTable, ListFilter, PageTop},
        data() {
            return {
                tableHeading: ['Sl', 'Name', 'Action'],
            }
        },
        methods: {

        },
        mounted() {
            this.getDataList();
        }
    }
</script>

<style scoped>


</style>