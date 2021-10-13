<template>
    <div>
        <page-top page-heading="Role Module List" modal-header="Role Module Permission" modal-id="formModal" button-text="Add Permission"></page-top>
        <template>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <data-table :table-heading="tableHeading">
                        <template v-slot:filter>
                            <list-filter @click="getDataList()">
                                <div class="col-8"></div>
                                <div class="col-3">
                                    <select class="form-control form-control-sm" v-model="filter.role_id">
                                        <option value="">Select</option>
                                        <template v-for="(role, index) in requiredData.role">
                                            <option :value="role.id">{{role.name}}</option>
                                        </template>
                                    </select>
                                </div>
                            </list-filter>
                        </template>
                        <template v-slot:data>
                            <tr v-for="(data, index) in dataList.date" :key="index">
                                <td>{{index+1}}</td>
                                <td>{{data.display_name}}</td>
                                <td>
                                    <template v-for="(eachPermission, p_index) in data.all_permissions">
                                        <a style="margin: 10px">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" @change="updatePermission($event,eachPermission,data)" v-model="eachPermission.checked">
                                                <span style="text-transform: capitalize">{{eachPermission.exact}}</span>
                                            </label>
                                        </a>
                                    </template>
                                </td>
                                <td>
                                    <button @click="dataDelete(index, data.id, `${urlGenerate()}/${data.id}?role_id=${data.role_id}`)" class="btn btn-sm"><i class="fa fa-trash text-danger"></i></button>
                                </td>
                            </tr>
                        </template>
                    </data-table>
                </div>
            </div>
        </template>
        <form-modal modal-id="formModal" modal-size="modal-xl" @submit="submitData({role_id:filter.role_id, data : dataList.modules}, 'formModal')">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Name</label>
                    <select v-validate="'required'" name="role_id" class="form-control" v-model="filter.role_id" @change="getDataList()">
                        <option value="">Select</option>
                        <template v-for="(role, index) in requiredData.role">
                            <option :value="role.id">{{role.name}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Permission</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(module, index) in dataList.modules">
                                <td><a style="margin: 10px">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" v-model="module.checked">
                                        <span style="text-transform: capitalize">{{module.display_name}}</span>
                                    </label>
                                </a></td>
                                <td>
                                    <template v-for="(eachPermission, p_index) in module.permissions">
                                        <a style="margin: 10px">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" v-model="eachPermission.checked">
                                                <span style="text-transform: capitalize">{{eachPermission.exact}}</span>
                                            </label>
                                        </a>
                                    </template>
                                </td>
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
        name: "roleModuleList",
        components: {FormModal, DataTable, ListFilter, PageTop},
        data() {
            return {
                tableHeading: ['Sl', 'Name','Permissions', 'Action'],
                permissionList : [],
                isOpenPermissionModal : false,
            }
        },
        methods: {
            updatePermission : function (event, eachPermission, data) {
                const _this = this;
                this.$store.commit('httpRequest', true);

                if (event.target.checked){
                    eachPermission.checked = true;
                } else{
                    eachPermission.checked = false;
                }

                var permission = {
                    permission_id:eachPermission.id,
                    module_id : data.id,
                    role_id:data.role_id,
                    checked:eachPermission.checked,
                };

                var URL = _this.urlGenerate()+'/update';
                _this.axios({method: 'put', url: URL, data: permission}).then(function (response) {
                    var retData = response.data;
                    if (parseInt(retData.status) === 2000) {
                        _this.$toastr('success', retData.message, 'Success');
                    }
                    if (parseInt(retData.status) === 3000) {
                        _this.$toastr('warning', retData.message, 'Deleted');
                    }
                    _this.getDataList();
                }).catch(function (error) {
                    _this.$toastr('error', 'Something Wrong', 'Error');
                    _this.$router.push({path:'/admin/dashboard'})
                });
            },
            dataDelete : function (index, id, url) {
                const _this = this;
                this.deleteInformation(index, id, url, function ($retData) {
                    _this.getConfigurations();
                });
            },
            submitData : function (formData, modal) {
                const _this = this;
                _this.submitForm(formData, modal, function (retData) {
                    _this.getConfigurations();
                })
            }
        },
        mounted() {
            this.filter.role_id = '';
            this.getDataList();
            this.getGeneralData(['role']);
        }
    }
</script>

<style scoped>


</style>