<template>
    <div>
        <page-top page-heading="Configuration List" modal-header="Add new Configuration" modal-id="formModal" button-text="New Configuration"></page-top>
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
                                <td>{{data.key}}</td>
                                <td>{{data.display_name}}</td>
                                <td>
                                    <span v-if="data.type == 'file'">
                                        <img style="height: 20px" :src="getImage(data.value)">
                                    </span>
                                    <span v-else>{{ showData(data,'value')}}</span>
                                </td>
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
                    <label>Key</label>
                    <input v-validate="'required'" name="key" type="text" v-model="formObject.key" class="form-control">
                </div>
                <div class="col-md-12 form-group">
                    <label>Display name</label>
                    <input v-validate="'required'" name="display_name" type="text" v-model="formObject.display_name" class="form-control">
                </div>
                <div class="col-md-12 form-group">
                    <label>Type</label>
                    <select v-validate="'required'" name="display_name" v-model="formObject.type" class="form-control">
                        <option value="">Select</option>
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label>Value</label>
                    <template v-if="formObject.type == 'text'" >
                        <input class="form-control form-control-sm" v-validate="'required'" data-vv-as="Value" v-model="formObject.value" name="value" type="text">
                    </template>
                    <template v-if="formObject.type == 'file'" >
                        <input class="form-control form-control-sm" @change="onFileSelected($event, 'file')"  data-vv-as="Value" name="value" type="file">
                    </template>
                    <template v-if="formObject.type == 'textarea'" >
                        <textarea class="form-control form-control-sm" v-validate="'required'" data-vv-as="Value" v-model="formObject.value" style="width: 100%;"></textarea>
                    </template>
                </div>
            </div>
        </form-modal>
    </div>
</template>

<script>
    import PageTop from "../../component/layouts/pageTop";
    import ListFilter from "../../component/layouts/listFilter";
    import DataTable from "../../component/layouts/dataTable";
    import FormModal from "../../component/layouts/formModal";

    export default {
        name: "configurationList",
        components: {FormModal, DataTable, ListFilter, PageTop},
        data() {
            return {
                tableHeading: ['Sl', 'Key', 'Display name', 'Value', 'Action'],
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