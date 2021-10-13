<template>
    <div>
        <page-top page-heading="User List" modal-header="New User" modal-id="formModal" button-text="New User"></page-top>
        <template>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <data-table :table-heading="tableHeading">
                        <template v-slot:filter>
                            <list-filter @click="getDataList()">
                                <input v-model="filter.keyword" type="search" class="form-control form-control-sm" placeholder="Search">
                            </list-filter>
                        </template>
                        <template v-slot:data>
                            <tr v-for="(data, index) in dataList.data" :key="index">
                                <td>{{index+1}}</td>
                                <td>{{data.name}}</td>
                                <td>{{data.email}}</td>
                                <td>{{data.username}}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm" @click="editData(data,data.id)"><i class="fa fa-edit text-warning"></i></button>
                                    <button class="btn btn-sm" @click="deleteInformation(index, data.id)"><i class="fa fa-trash text-danger"></i></button>
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
                <div class="col-md-12 form-group">
                    <label>Email</label>
                    <input v-validate="'required'" name="email" type="text" v-model="formObject.email" class="form-control">
                </div>
                <div class="col-md-12 form-group">
                    <label>Username</label>
                    <input v-validate="'required'" name="email" type="text" v-model="formObject.username" class="form-control">
                </div>
                <div class="col-md-12 form-group" v-if="formType === 1">
                    <label>Password</label>
                    <input v-validate="'required'" name="password" type="password" v-model="formObject.password" class="form-control">
                </div>
                <div class="col-md-12 form-group">
                    <label>Role</label>
                    <select v-model="formObject.role_id" class="form-control">
                        <option value="1">Admin</option>
                    </select>
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
        name: "userList",
        components: {FormModal, DataTable, ListFilter, PageTop},
        data() {
            return {
                tableHeading: ['Sl', 'Name', 'Email', 'Username', 'Action'],
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