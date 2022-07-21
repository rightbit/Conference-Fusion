<template>
    <option value="null" disabled hidden selected>Select a status</option>
    <option v-for="status in statuses" v-bind:value="status.id">{{ status.status }}</option>
</template>

<script>
    // ***Admin Only***
    export default {
        data: function() {
            return {
                statuses: [],
            }
        },
        mounted() {
            this.loadStatuses();
        },
        methods: {
            loadStatuses: function () {
                axios.get('/api/admin/session-status')
                    .then((response) => {
                        this.statuses = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the session statuses`);
                    });
            },
        }

    }
</script>
