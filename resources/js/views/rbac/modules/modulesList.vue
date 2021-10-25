<template>
    <div>
        <page-top page-heading="Module List" modal-header="New Module" modal-id="formModal" button-text="New Configuration" :default-button="false">
            <button v-if="can('add')" @click="addModule()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fa fa-plus fa-sm text-white-50"></i>Add Module
            </button>
        </page-top>
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
                            <template v-for="(data, index) in dataList.data">
                                <tr>
                                    <td>{{index+1}}</td>
                                    <td>{{data.display_name}}</td>
                                    <td>
                                        <template v-for="(permission, p_index) in data.permissions">
                                            <a class="btn">{{permission.exact}}</a>
                                        </template>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm" @click="editData(data,data.id)"><i class="fa fa-edit text-warning"></i></button>
                                        <button class="btn btn-sm" @click="deleteInformation(index, data.id)"><i class="fa fa-trash text-danger"></i></button>
                                    </td>
                                </tr>
                                <template v-for="(data, index2) in data.submenu">
                                    <tr style="background: #f6f6f6;">
                                        <td>{{index+1}}.{{index2+1}}</td>
                                        <td>{{data.display_name}}</td>
                                        <td>
                                            <template v-for="(permission, p_index) in data.permissions">
                                                <a class="btn">{{permission.exact}}</a>
                                            </template>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm" @click="editData(data,data.id)"><i class="fa fa-edit text-warning"></i></button>
                                            <button class="btn btn-sm" @click="deleteInformation(index, data.id)"><i class="fa fa-trash text-danger"></i></button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </data-table>
                </div>
            </div>
        </template>
        <form-modal modal-id="formModal" modal-size="modal-md" @submit="submitData(formObject,'formModal')">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Name</label>
                    <input v-validate="'required'" name="name" type="text" v-model="formObject.name" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Display name</label>
                    <input v-validate="'required'" name="display_name" type="text" v-model="formObject.display_name" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Link/URL</label>
                    <input v-validate="'required'" name="link" type="text" v-model="formObject.link" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label>Parent</label>
                    <select name="parent_id" class="form-control" v-model="formObject.parent_id">
                        <option value="0">select</option>
                        <template v-for="(parent, index) in parentModule">
                            <option :value="parent.id">{{parent.display_name}}</option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h3><strong>Permission</strong></h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Permission name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(permission, index) in formObject.permissions">
                                <th>{{index+1}}</th>
                                <th><input class="form-control" v-model="permission.exact"></th>
                                <th>
                                    <a v-if="index === formObject.permissions.length-1" @click="addRow(formObject.permissions, {exact : ''})" class="btn">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <a @click="deleteRow(formObject.permissions, index)" class="btn">
                                        <i class="fa fa-close text-danger"></i>
                                    </a>
                                </th>
                            </tr>
                        </tbody>
                    </table>
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
        name: "modulesList",
        components: {FormModal, DataTable, ListFilter, PageTop},
        data() {
            return {
                tableHeading: ['Sl', 'Name', 'Permissions', 'Action'],
            }
        },
        methods: {
            addModule : function () {
                this.$set(this.formObject, 'permissions', [{
                    exact : '',
                }]);
                this.openModal('formModal', 'Add new Module')
            },
            submitData : function (formObject, modal) {
                const _this = this;
                this.submitForm(formObject, modal, function (retData) {
                    _this.getConfigurations();
                });
            }
        },
        mounted() {
            this.getDataList();
        },
        computed : {
            parentModule(){
                if (this.dataList.data !== undefined){
                    var newArray = this.dataList.data.filter(function(item) {
                        return parseInt(item.parent_id) === 0;
                    });
                    return newArray;
                }
                return [];

            }
        }
    }
</script>

<style scoped>


</style>