<template>
    <div class="dataTables_wrapper dt-bootstrap4">
        <slot name="filter"></slot>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-bordered" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th v-for="(heading, index) in tableHeading" :key="index" :class="{'width_15' : index === 0}">{{heading}}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <slot name="data"></slot>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "dataTable",
        props: {
            tableHeading: {
                type: [Array, Object],
                default: []
            }
        },
    }
</script>

<style scoped>
    .table td {
        padding: 5px 10px;
        vertical-align: center;
    }

    th.width_15 {
        width: 5%;
    }

    thead tr {
        background: #f8f9fc;
    }
</style>