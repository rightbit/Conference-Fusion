<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-3" id="participant-data-card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0 click-collapse" data-bs-toggle="collapse" data-bs-target="#site-config-body" aria-expanded="false">
                            Site Configuration
                            <i class="bi bi-chevron-down float-start me-2"></i>
                        </div>
                        <button class="btn btn-warning" @click="toggleLock"><i :class="['bi', locked ? 'bi-lock' : 'bi-unlock']"></i> Unlock to Edit</button>
                    </div>

                    <div class="card-body collapse" id="site-config-body">
                        <table class="table">
                            <tbody>
                            <tr scope="row" v-for="config in configs">
                                <td>{{ config.key }}</td>
                                <td class="w-25">{{ config.description }}</td>
                                <td><input type="text" class="form-control form-control-sm" :id="config.key" v-model="config.value" :disabled="disabled" required /></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary me-2" @click="getConfig(config)">Reset</button>
                                    <button class="btn btn-sm btn-dark" :disabled="disabled" @click="putConfig(config)">Save</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mb-3" v-for="(cat_infos, cat) in user_info_data">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0 click-collapse card-title" data-bs-toggle="collapse" :data-bs-target="'#cat-body-'+cat" aria-expanded="true">
                            {{ cat }} Info
                            <i class="bi bi-chevron-down float-start me-2"></i>
                        </div>
                        <button class="btn btn-warning" @click="toggleLock"><i :class="['bi', locked ? 'bi-lock' : 'bi-unlock']"></i> Unlock to Edit</button>
                    </div>

                    <div class="card-body collapse show" :id="'cat-body-'+cat">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Key Name</th>
                                <th>Key Label</th>
                                <th>Key Options (Optional, comma seperated list)</th>
                                <th>Require an answer</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="cat_info in cat_infos">
                                <td><input type="text" class="form-control form-control-sm" v-model="cat_info.name" :disabled="disabled" required /></td>
                                <td><input type="text" class="form-control form-control-sm" v-model="cat_info.label" :disabled="disabled" /></td>
                                <td><input type="text" class="form-control form-control-sm" v-model="cat_info.options" :disabled="disabled" /></td>
                                <td><input class="form-check-input me-2" type="checkbox" v-model="cat_info.required" :id="'required-'+cat_info.id">
                                    <label class="form-check-label" :for="'required-'+cat_info.id">
                                        Required
                                    </label></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary me-2 my-1" @click="getInfoData(cat_info)">Reset</button>
                                    <button class="btn btn-sm btn-dark" :disabled="disabled" @click="putInfoData(cat_info)">Save</button>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" :disabled="disabled" @click="deleteInfoData(cat_info)"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body" id="addinfo">
                        <h4 class="ms-2 mb-4"> Add new info</h4>
                        <form id="newinfo" class="row g-2 align-items-center" @submit.prevent="addInfoData()">
                            <div class="col-md-2">
                                <select id="inputCategory" v-model="new_info.type" class="form-select" aria-label="select category" required>
                                    <option value="" disabled selected>Choose a type</option>
                                    <option value="participant">Participant Info</option>
                                    <option value="personal">Personal Info</option>
                                    <option value="social">Social Info</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" v-model="new_info.name" class="form-control" id="inputName" placeholder="Name (lower case, dashes ok)" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" v-model="new_info.label" class="form-control" id="inputOptions" placeholder="Profile form label">
                            </div>
                            <div class="col-md-4">
                                <input type="text" v-model="new_info.options" class="form-control" id="inputOptions" placeholder="Options (optional, comma separated)">
                            </div>
                            <div class="col-md-1">
                                <div class="form-check">
                                    <input v-model="new_info.required" class="form-check-input" type="checkbox" id="requiredCheck">
                                    <label class="form-check-label" for="requiredCheck">
                                        Required
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <button type="submit" class="btn btn-primary btn-sm">Add new</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            configs: [],
            user_info_data: {
                participant: [],
                personal: [],
                social: [],
            },
            new_info: {
                type: '',
                name: '',
                label: '',
                options: '',
                required: null,
            },
            disabled: 1,
            locked: 1,
        }
    },
    mounted() {
        this.loadConfigs();
        this.loadInfoData('participant');
        this.loadInfoData('personal');
        this.loadInfoData('social');
    },
    methods: {
        loadConfigs: function() {
            axios.get('/api/admin/configuration')
                .then((response) => {
                    this.configs = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the configurations`);
                });
        },
        getConfig: function(config) {
            axios.get(`/api/admin/configuration/${config.id}`)
                .then((response) => {
                   config.value = response.data.data.value;
                   this.$toast.warning(`The ${config.key} was successfully reset`);
                })
                .catch((error) => {
                    this.$toast.error(`Could not reset ${config.key}`);
                });
        },
        putConfig: function(config) {
            axios.put(`/api/admin/configuration/${config.id}`, config)
                .then((response) => {
                    this.$toast.success(`The ${config.key} was successfully updated`);
                })
                .catch((error) => {
                    this.$toast.error(`Could not save ${config.key}<br />` + error.response.data.message);
                });
        },
        getInfoData: function(cat_info) {
            axios.get(`/api/profile/user-info-data/${cat_info.id}`)
                .then((response) => {
                    let r = response.data.data;
                    cat_info.id = r.id;
                    cat_info.type = r.type;
                    cat_info.name = r.name;
                    cat_info.label = r.label;
                    cat_info.options = r.options;
                    cat_info.required = r.required;
                    this.$toast.warning(`The ${cat_info.name} info was successfully reset`);
                })
                .catch((error) => {
                    this.$toast.error(`Could not reset ${cat_info.name}`);
                });
        },
        putInfoData: function(datainfo) {
            axios.put(`/api/profile/user-info-data/${datainfo.id}`, datainfo)
                .then((response) => {
                    this.$toast.success(`The ${datainfo.name} info was successfully updated`);
                })
                .catch((error) => {
                    this.$toast.error(`Could not save ${datainfo.name}<br />` + error.response.data.message);
                });
        },
        addInfoData: function() {
            axios.post(`/api/profile/user-info-data`, this.new_info)
                .then((response) => {a
                    this.$toast.success(`The ${this.new_info.name} info was successfully added`);
                    this.loadInfoData(this.new_info.type)
                    this.new_info.name = '';
                    this.new_info.label = '';
                    this.new_info.options = '';
                    this.new_info.required = null;
                    this.new_info.type = '';
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the new info<br />` + error.response.data.message);
                });
        },
        loadInfoData: function(category) {
            axios.get('/api/profile/user-info-data', { params: { cat: category } })
                .then((response) => {
                    this.user_info_data[category] = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the category`);
                });
        },
        deleteInfoData: function(cat_info) {
            if(confirm("Do you really want to delete?")) {
                axios.delete(`/api/profile/user-info-data/${cat_info.id}`)
                    .then((response) => {
                        this.$toast.success(`${cat_info.name} succesfully deleted`);
                        this.loadInfoData(cat_info.type);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not delete ${cat_info.name}`);
                    });
            }
        },
        toggleLock: function() {
            //TODO fix this to unlock only individual lines. See TrackList.vue
            this.disabled = !this.disabled;
            this.locked = !this.locked;
        }
    }
}
</script>
