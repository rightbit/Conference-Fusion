<template>
    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row pb-3"  v-if="allowForm">
            <form class="needs-validation row" novalidate @submit.prevent="addUpdateUser">
                <div class="col-sm-12 mt-2 text-right"><button class="btn btn-primary profile-button float-end" type="submit">Save Profile</button></div>
                <div class="col-md-3 border-right">
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                        <img class="rounded-circle mt-5" style="width:150px;" :src="profileImage" />
                        <span class="font-weight-bold" v-if="user.info.badge_name">{{ user.info.badge_name }}</span>
                        <button v-if="user.id && user.id != 0 && user.info.id !== null" type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#profileModal">Edit <i class="bi bi-pencil-square"></i></button>
                        <div v-else class="btn btn-outline-secondary w-75 mt-2" >Update profile before uploading image</div>
                        <div v-if="this.superAdmin" class="w-100">
                            <hr />
                            <h5>User permissions</h5>
                            <div v-for="(permission_name, permission) in this.permissions" class="form-check text-start ms-3">
                                <input class="form-check-input" :id="'perm_'+permission" type="checkbox" v-model="this.userPermissionsValue[permission]" @change="updateUserPermission(permission, permission_name)" :disabled="permissionsDisabled">
                                <label class="form-check-label" :for="'perm_'+permission">
                                    {{ permission_name }}
                                </label>
                            </div>
                        </div>
                        <div v-if="this.viewAdmin" class="w-100">
                            <hr />
                            <h5>Session participation info</h5>
                            <button class="btn btn-outline-secondary w-100 mb-2 text-start"><i class="bi bi-eye-fill"></i> Interests: {{ this.sessionCounts.interest }}</button><br />
                            <button class="btn btn-outline-secondary w-100 mb-2 text-start"><i class="bi bi-eye-fill"></i> Participant: {{ this.sessionCounts.participant }}</button><br />
                            <button class="btn btn-outline-secondary w-100 text-start"><i class="bi bi-eye-fill"></i> Presenter: {{ this.sessionCounts.presenter }}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 border-right">
                    <div class=" py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-right">Profile settings</h4>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="firstName">First name</label>
                                <input type="text" class="form-control" id="firstName" placeholder="First name" v-model="user.first_name" required>
                                <div class="invalid-feedback">
                                    Please provide a first name
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="lastName">Last name</label>
                                <input type="text" class="form-control" id="lastName" placeholder="Last name" v-model="user.last_name" required>
                                <div class="invalid-feedback">
                                    Please provide a last name
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <label for="email">Login Email</label>
                                <input type="text" class="form-control" id="email" placeholder="Login email" v-model="user.email"  required>
                                <div class="invalid-feedback">
                                    Email address is required
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="contactEmail">Contact email</label><small id="contactEmailHelpBlock" class="form-text text-muted" >
                                Optional, if different
                            </small>
                                <input type="text" class="form-control" id="contactEmail" placeholder="Contact email"  v-model="user.info.contact_email" />
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                               <a href="/password/reset">Change my password</a>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                                <label for="badge_name">Badge Name</label>
                                <input type="text" class="form-control" id="badge_name" placeholder="Badge name" v-model="user.info.badge_name" required>
                                <div class="invalid-feedback">
                                    Please provide a badge name
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="biography">Biography</label>
                                <textarea id="biography" class="form-control" placeholder="Your biography (to publish publicly)" v-model="user.info.biography" ></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes">Info and notes</label>
                                <textarea id="notes" class="form-control" placeholder="General notes for the staff about you (staff view only)"  v-model="user.info.notes" ></textarea>
                            </div>
                        </div>
                        <div class="row g-2 mb-4">
                            <div class="col-md-12">
                                <label for="website">Website</label>
                                <input type="text" class="form-control" id="website" placeholder="Badge name" v-model="user.info.website">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-12 mb-2"><span class="h4">Sell on Consignment</span><br/>
                                If you would like to request a book partner to possibly carry your books on consignment or through traditional sale, please enter the information below.
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label for="book1title">Book 1</label>
                        </div>
                        <div class="row g-2  mb-2">
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book1_title" placeholder="Title" />
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book1_author" placeholder="Author" />
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book1_isbn" placeholder="ISBN" />
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label for="book1title">Book 2</label>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book2_title"  placeholder="Title" />
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book2_author" placeholder="Author" />
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book2_isbn" placeholder="ISBN" />
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label for="book1title">Book 3</label>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book3_title"  placeholder="Title" />
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book3_author" placeholder="Author" />
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" v-model="user.info_consignment.book3_isbn" placeholder="ISBN" />
                            </div>
                        </div>
                        <div class="mt-4 form-group" v-if="!user.info.agree_to_terms">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="user.info.agree_to_terms" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Agree to site <a href="/terms-and-policies" target="_blank">terms and policies</a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree before submitting
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="user.info.recording_permission" id="recorded">
                                <label class="form-check-label" for="recorded">
                                    Give permission to be photographed and recorded
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="user.info.share_email_permission" id="shareEmail">
                                <label class="form-check-label" for="shareEmail">
                                    Give permission to share your email with other panelists
                                </label>
                            </div>
                        </div>
                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit">Save Profile</button></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row mt-5 g-2 form-group">
                        <div class="d-flex justify-content-between align-items-center experience"><span class="h4">Personal Info</span></div>
                        <partials-user-info-data v-if="this.finished_loading" :category="'personal'" :user-info="user.info.personal_data" @changeInfoData="updateInfoData" />
                    </div>
                    <div class="row mt-3 g-2 form-group">
                        <div class="d-flex justify-content-between align-items-center experience"><span class="h4">Social Info</span></div>
                        <partials-user-info-data v-if="this.finished_loading" :category="'social'" :user-info="user.info.social_data" @changeInfoData="updateInfoData" />
                    </div>
                    <div class="row mt-3 g-2 form-group">
                        <div class="d-flex justify-content-between align-items-center experience"><span class="h4">Participant Info</span></div>
                        <partials-user-info-data v-if="this.finished_loading" :category="'participant'" :user-info="user.info.participant_data" @changeInfoData="updateInfoData" />
                    </div>

                </div>
            </form>
            <partials-user-profile-image-modal :user-id="this.userId" @changeImage="reloadProfileImage" />
        </div>
        <div v-else class="p-5 text-center">
            <h1>Error 404</h1>
            User not found
        </div>
    </div>
</template>


<script>
export default {
    props: ['userId', 'currentUser', 'superAdmin', 'viewAdmin', 'permissions'],
    data: function() {
        return {
            user: {
                info: {
                    personal_data: {'':''},
                    social_data: {'':''},
                    participant_data: {'':''},
                },
                info_consignment: {}
            },
            profileImage: '/images/app/blank-profile.png',
            allowForm: true,
            finished_loading: false,
            permissionsDisabled: true,
            userPermissions: {},
            userPermissionsValue: {},
            sessionCounts: {
                interest: 0,
                participant: 0,
                presenter: 0,
            },
        }
    },
    mounted() {
        this.getUser();
        this.getUserSessionTotals();
    },
    methods: {
        getUser: function() {
            // Don't try to load if a new user
            if(this.userId === 0) {
                this.finished_loading = true;
                return;
            }
            axios.get(`/api/profile/user/${this.userId}`)
                .then((response) => {
                    this.user = response.data.data;
                    if(!this.user.info.personal_data) {
                        this.user.info.personal_data = {};
                    }
                    if(!this.user.info.social_data) {
                        this.user.info.social_data = {};
                    }
                    if(!this.user.info.participant_data) {
                        this.user.info.participant_data = {};
                    }
                    if(this.user.info.profile_image) {
                        this.profileImage = this.user.info.profile_image;
                    }
                    if(!this.user.info_consignment) {
                        this.user.info_consignment = {};
                    }
                    this.allowForm = true;
                    if(this.superAdmin) {
                        this.getUserPermissions();
                    }
                    this.finished_loading = true;

                })
                .catch((error) => {
                    this.$toast.show(`Could not find the user. Click the back button to try again.`, {
                        type: 'error',
                        duration: false,
                        onClick: ''
                    });
                    this.allowForm = false;
                });
        },
        getUserSessionTotals: function() {
            if(this.userId === 0) {
                return
            }
            axios.get(`/api/user-session-totals/${this.userId}`)
                .then((response) => {
                    this.sessionCounts = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Error loading the session counts`);
                });
        },
        getUserPermissions: function() {
            if(!this.user.id) {
               this.$toast.error(`Please create the user before setting permissions`);
            }

            axios.get(`/api/admin/permissions`, { params: {user_id: this.user.id}})
                .then((response) => {
                    var listByNames = {};
                    var permissionValues = {};
                    response.data.data.forEach(function(item) {
                        permissionValues[item.permission] = true;
                        listByNames[item.permission] = item;
                    });
                    this.userPermissionsValue = permissionValues;
                    this.userPermissions = listByNames;

                    if(this.superAdmin && this.currentUser !== this.userId) {
                        this.permissionsDisabled = false;
                    }

                })
                .catch((error) => {
                    this.$toast.error(`Could not load the user permissions`);
                });

        },
        addUpdateUser: function() {
            if(this.user.id) {
                axios.put(`/api/profile/user/${this.user.id}`, this.user )
                    .then((response) => {
                        this.$toast.success(`Updated the user successfully`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save the user info<br />` + error.response.data.message);
                    });
            } else {
                axios.post('/api/profile/user', this.user)
                    .then((response) => {
                        this.user = response.data.data;
                        this.$toast.success(`Created a new user successfully`);
                        window.history.replaceState(null, "", '/user-profile/'+this.user.id );
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not create a new user<br />` + error.response.data.message);
                    });
            }
        },
        reloadProfileImage: function(event){
            this.profileImage = event + '?' + Date.now(); //create unique url to reload image
        },
        updateInfoData: function(event){
           this.user.info[event.category][event.name] = event.value;
        },
        updateUserPermission: function(permission, name) {
            if(this.userPermissionsValue[permission]) {
                axios.post(`/api/admin/permissions`,  {user_id: this.userId, permission: permission})
                    .then((response) => {
                        this.userPermissions[permission] = response.data.data;
                        this.$toast.success(`Added the ${name} permission`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not add the ${name} permission`);
                    })
            } else {
                axios.delete(`/api/admin/permissions/${this.userPermissions[permission].id}`)
                    .then((response) => {
                        this.userPermissions[permission] = {};
                        this.$toast.success(`Removed the ${name} permission`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not delete the ${name} permission`);
                    })
            }

        }
    }

}
</script>

