<template>
    <div>
        <page-top page-heading="Permission List" modal-header="New Permission" modal-id="formModal" button-text="New Permission"></page-top>
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
                                <td>{{data.display_name}}</td>
                                <td>
                                    <template v-for="(permission, p_index) in data.permissions">
                                        <a class="btn">{{permission.exact}}</a>
                                    </template>
                                </td>
                                <td>
                                    <button class="btn btn-sm" @click="editData(data,data.id)"><i class="fa fa-edit text-warning"></i></button>
                                    <button class="btn btn-sm" @click="deleteInformation(index, data.id)"><i class="fa fa-trash text-danger"></i></button>
                                </td>
                            </tr>
                        </template>
                    </data-table>
                    <pagination previousText="PREV" nextText="NEXT" :data="dataList" @paginateTo="getDataList"/>
                </div>
            </div>
        </template>
        <form-modal modal-id="formModal" modal-size="modal-xl" @submit="submitForm()">
            <div class="row" v-for="(form, f_index) in formData">
                <div class="col-md-3 text-right">
                    <label>{{f_index}}</label>
                </div>
                <div class="col-md-7 form-group">
                    <input v-validate="form.rules" :name="f_index" type="text" v-model="form.value" class="form-control">
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
        name: "permissionList",
        components: {FormModal, DataTable, ListFilter, PageTop},
        data() {
            return {
                tableHeading: ['Sl', 'Module', 'Permissions','Action'],
            }
        },
        methods: {
            submitForm: function () {
                console.log(this.formData);
            }
        },
        mounted() {
            this.getDataList();
        }
    }
</script>

<style scoped>


</style>