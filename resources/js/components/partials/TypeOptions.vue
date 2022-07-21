<template>
    <option value="null" disabled hidden selected>Select a type</option>
    <option v-for="type in types" v-bind:value="type.id">{{ type.name }}</option>
</template>

<script>
    export default {
        data: function() {
            return {
                types: [],
            }
        },
        mounted() {
            this.loadTypes();
        },
        methods: {
            loadTypes: function () {
                axios.get('/api/type-list')
                    .then((response) => {
                        this.types = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the session types`);
                    });
            },
        }

    }
</script>
