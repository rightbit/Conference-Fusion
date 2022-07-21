<template>
    <option value="null" disabled hidden selected>Select a track</option>
    <option v-for="track in tracks" v-bind:value="track.id">{{ track.name }}</option>
</template>

<script>
    export default {
        data: function() {
            return {
                tracks: [],
            }
        },
        mounted() {
            this.loadTracks();
        },
        methods: {
            loadTracks: function () {
                axios.get('/api/track-list')
                    .then((response) => {
                        this.tracks = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the tracks`);
                    });
            },
        }

    }
</script>
