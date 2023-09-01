<style>
.table-free-width td, th {
    min-width: 10px;
}
</style>

<template>
    <div class="">
        <h2>Potential Participant List</h2>
        <div class="float-end">
            <select v-model="type_id" @change="loadReport" class="form-select">
                <option value="1">Panelists</option>
                <option value="2">Presenters</option>
            </select>
        </div>
        <h6>Sortable list of potential panelists/presenters</h6>
        {{ data.length }} people found

        <table class="table table-scrollable table-free-width table-sm table-striped bg-white mt-2">
            <thead style="z-index: 500">
            <tr class="bg-secondary text-white">
                <td></td>
                <td class="ts-sc"><button class="btn btn-link" @click="sortList('last_name')"><i v-if="sort == 'last_name'" :class="[desc ? 'bi bi-caret-down' : 'bi bi-caret-up']"></i>Name</button></td>
                <td class="ts-sc">Badge Name</td>
                <td class="ts-sc text-center"><button class="btn btn-link" @click="sortList('total')"><i v-if="sort == 'total'" :class="[desc ? 'bi bi-caret-down' : 'bi bi-caret-up']"></i>Total</button></td>
            </tr>
            </thead>
            <tbody >
            <tr v-for="d in data">
                <td><a :href="`/admin/user-profile/${d.user_id}`" target="_blank" class="btn btn-link">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </td>
                <td>
                    <a :href="`/admin/user-profile/${d.user_id}`" target="_blank" class="text-decoration-none text-body fw-bold">
                        {{ d.last_name }}, {{ d.first_name }}
                    </a>
                </td>
                <td class="text-nowrap">{{d.badge_name}}</td>
                <td class="text-center">{{ d.total }}</td>
            </tr>
            </tbody>
        </table>
    </div>

</template>


<script>
import moment from 'moment';

export default {
    props: ['conferenceId'],
    data: function () {
        return {
            data: {},
            type_id: 1,
            sort: 'last_name',
            desc: false,

        }
    },
    mounted() {
        this.loadReport();

    },
    methods: {
        loadReport: function() {
            axios.get(`/api/admin/report/potential-panelist-list/${this.conferenceId}`, { params: { type_id: this.type_id, sort: this.sort, desc: this.desc } })
                .then((response) => {
                    this.data = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the report`);
                });
        },
        sortList: function(sortVal) {
            if(this.sort == sortVal) {
                this.desc = !this.desc;
            } else {
                this.sort = sortVal;
                this.desc = false;
            }
            this.loadReport();
        },
    },
}
</script>
