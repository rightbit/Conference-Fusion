<template>
    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row pb-3"  v-if="allowForm">
            <form class="needs-validation row" novalidate @submit.prevent="addUpdateUser()">
                <div class="row mt-4">
                    <div class="col-sm-10 ps-4"><h2>User profile</h2></div>
                    <div class="col-sm-2 text-right p-0"><button class="btn btn-primary profile-button float-end" type="submit">Save Profile</button></div>
                </div>
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
                            <button v-if="true" class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#user-token-modal">Get API Token</button>

                        </div>
                        <div v-if="this.viewAdmin" class="w-100">
                            <hr />
                            <h5>Session participation info</h5>
                            <button class="btn btn-outline-secondary w-100 mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#user-session-data" @click="getModalSessionData(1, 'interests')">
                                <i class="bi bi-eye-fill"></i> Interests: {{ sessionCounts.interest }}
                            </button>
                            <button class="btn btn-outline-secondary w-100 mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#user-session-data" @click="getModalSessionData(1, 'panelist')">
                                <i class="bi bi-eye-fill"></i> Panelist: {{ sessionCounts.panelist }}
                            </button>
                          <button class="btn btn-outline-secondary w-100 mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#user-session-data" @click="getModalSessionData(1, 'presenter')">
                            <i class="bi bi-eye-fill"></i> Presenter: {{ sessionCounts.presenter }}
                          </button>
                          <button  v-if=" sessionCounts.other > 0" class="btn btn-outline-secondary w-100 mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#user-session-data" @click="getModalSessionData(1, 'other')">
                            <i class="bi bi-eye-fill"></i> Other: {{ sessionCounts.other }}
                          </button>
                            <hr />
                            <h5>Staff notes <span class="text-secondary">(private)</span></h5>
                            <textarea class="form-control" v-model="user.info.staff_notes" rows="4"></textarea>
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
                        <div class="form-groupm mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="user.info.share_email_permission" id="shareEmail">
                                <label class="form-check-label" for="shareEmail">
                                    Give permission to share your email with other panelists
                                    <i class="bi bi-question-circle text-primary"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="right"
                                       title="Your email can be shared with moderators and panelists in the same session with you."></i>
                                </label>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-12">
                              <i class="bi bi-key-fill"></i>
                              <button v-if="user.id && user.id != 0 && user.info.id !== null" type="button" class="btn btn-link ps-1" data-bs-toggle="modal" data-bs-target="#updatePasswordModal">
                                Change password
                              </button>
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
                                <textarea rows="6" id="biography" class="form-control" placeholder="Your biography (to publish publicly)" v-model="user.info.biography" ></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes">Info and notes</label>
                                <textarea rows="6" id="notes" class="form-control" placeholder="General notes for the committee about you (not public). Use this area to list accomplishments and other information about you not on your bio to help the committee know you better. Also, please include special accommodations and any potential conflicts or concerns"  v-model="user.info.notes" ></textarea>
                            </div>
                        </div>
                        <div class="row g-2 mb-4">
                            <div class="col-md-12">
                                <label for="website">Website</label>
                                <input type="text" class="form-control" id="website" placeholder="Public website url" v-model="user.info.website">
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
                        <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit">Save Profile</button></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row mt-5 g-2 form-group">
                        <div class="d-flex justify-content-between align-items-center experience"><span class="h4">Personal Info</span></div>
                        <partials-user-info-data v-if="finished_loading" :category="'personal'" :user-info="user.info.personal_data" @changeInfoData="updateInfoData" />
                    </div>
                    <div class="row mt-3 g-2 form-group">
                        <div class="d-flex justify-content-between align-items-center experience"><span class="h4">Social Info</span></div>
                        <partials-user-info-data v-if="finished_loading" :category="'social'" :user-info="user.info.social_data" @changeInfoData="updateInfoData" />
                    </div>
                    <div class="row mt-3 g-2 form-group">
                        <div class="d-flex justify-content-between align-items-center experience"><span class="h4">Participant Info</span></div>
                        <partials-user-info-data v-if="finished_loading" :category="'participant'" :user-info="user.info.participant_data" @changeInfoData="updateInfoData" />
                    </div>
                </div>
            </form>
            <partials-user-profile-image-modal :user-id="this.userId" @changeImage="reloadProfileImage" />
            <partials-user-update-password-modal :user-id="this.userId" />
        </div>
        <div v-else class="p-5 text-center">
            <h1>Error 404</h1>
            User not found
        </div>

    </div>
    <div  v-if="this.viewAdmin" class="modal modal-lg fade" id="user-session-data" aria-hidden="true">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Session {{ this.modalSessionTitle }} </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped" v-if="this.modalSessionData.length > 0">
                        <tbody>
                            <tr v-for="sessionData in modalSessionData">
                                <td v-if="this.viewAdmin">
                                    <a :href="'/admin/conference-session/' + sessionData.conference_session_id" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-up-right-circle"></i></a>
                                </td>
                                <td>{{ sessionData.conference_session.name }}</td>
                                <td>{{ sessionData.conference_session.type_name }}</td>
                                <td class="border-start">{{ sessionData.conference_session.track_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="text-center">
                        <h5><i class="bi bi-exclamation-diamond"></i > No sessions found</h5>

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <Pagination :data="modalSessionlaravelData" :limit="3" :show-disabled="false" @pagination-change-page="getModalSessionData" />

                </div>
            </div>
        </div>
    </div>
    <div  v-if="this.viewAdmin" class="modal modal-lg fade" id="user-token-modal" aria-hidden="true">
      <div class="modal-dialog" >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">User API token</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Remember you won't get to see this token again. Please copy and save it immediately
            <button @click="getUserToken" class="btn btn-primary btn-sm" type="button">Get New API Token</button>
            <input type="text" v-if="userToken" class="form-control mt-2" :value="userToken" />
          </div>
        </div>
      </div>
    </div>
</template>


<script>
import { Tooltip } from "bootstrap";
export default {
    props: ['userId', 'currentUser', 'superAdmin', 'viewAdmin', 'permissions', 'conferenceId'],
    data: function() {
        return {
            user: {
                info: {
                    personal_data: {'':''},
                    social_data: {'':''},
                    participant_data: {'':''},
                },
            },
            profileImage: '/images/app/blank-profile.png',
            allowForm: true,
            finished_loading: false,
            permissionsDisabled: true,
            userPermissions: {},
            userPermissionsValue: {},
            userToken: '',
            sessionCounts: {
                interest: 0,
                panelist: 0,
                presenter: 0,
                other: 0,
            },
            modalSessionData: {},
            modalSessionTitle: '',
            modalSessionlaravelData: {},
        }
    },
    mounted() {
        new Tooltip(document.body, {
            selector: "[data-bs-toggle='tooltip']",
        }),
        this.getUser();
    },
    methods: {
        getUser: function() {
            // Don't try to load if no user
            if(this.userId === 0) {
                this.allowForm = false;
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
                    this.allowForm = true;
                    if(this.superAdmin) {
                        this.getUserPermissions();
                    }
                    this.finished_loading = true;
                    this.getUserSessionTotals();

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
            if(!this.user.id) {
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
        getModalSessionData: function(page = 1, cat = null) {
            this.modalSessionTitle = cat ? cat : this.modalSessionTitle;
            this.modalSessionData = {};
            axios.get(`/api/user/${this.user.id}/sessions/${this.modalSessionTitle}`, { params: { page: page }})
                .then((response) => {
                   this.modalSessionData = response.data.data;
                   this.modalSessionlaravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the user's session info`);
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
                axios.post('/api/profile/user', this.user )
                    .then((response) => {
                        this.user = response.data.data;
                        this.$toast.success(`Created a new user successfully`);
                        window.history.replaceState(null, "", '/user-profile/'+ this.user.id );
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

        },
        getUserToken: function() {
            axios.get(`/api/user-token/${this.userId}`)
                .then((response) => {
                    this.userToken = response.data.accessToken;
                })
                .catch((error) => {
                    this.$toast.error(`Could not fetch the user token`);
                });
        }

    }

}
</script>

