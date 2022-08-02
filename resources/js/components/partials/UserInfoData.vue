<template>
    <div class="col-12" v-for="info in infoData">
        <label class="labels">{{ info.label }}</label>
        <select
            v-if="info.options"
            v-model="this.userInfo[info.name]"
            v-on:change="emitNewValue(info.name, this.userInfo[info.name])"
            :id="info.name"
            class="form-control form-select"
        >
            <option value="undefined" hidden disabled selected>Choose one</option>
            <option v-for="option in info.options_array" :value="option.option">{{ option.display_option }}</option>
        </select>
        <input v-else
               type="text"
               class="form-control" :id="info.name"
               v-model="this.userInfo[info.name]"
               v-on:change="emitNewValue(info.name, this.userInfo[info.name])"
        />
    </div>
</template>

<script>
    export default {
        props: ['category', 'userInfo'],
        data: function() {
            return {
                infoData: [],
            }
        },
        mounted() {
            this.loadInfoData();
        },
        emits: ['changeInfoData'],
        methods: {
            loadInfoData: function () {
                if(this.category) {
                    axios.get('/api/profile/user-info-data/', {params: {cat: this.category}})
                        .then((response) => {
                            this.infoData = response.data.data;
                        })
                        .catch((error) => {
                            this.$toast.error(`Could not load the extra user info`);
                        });
                }
            },
            emitNewValue: function(name, value) {
                this.$emit("changeInfoData", {"category": this.category+"_data", "name":name, "value":value});
            }
        }

    }
</script>
