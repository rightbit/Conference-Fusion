<template>
    <div class="container">
        <div class="row justify-content-center" v-if="foundSession">
            <div class="col-md-10">
                <h2>Session Info</h2>
                <div v-if="page_message.value" class="alert alert-info col-lg-10 d-flex" role="alert">
                    <div><i class="bi bi-exclamation-circle-fill me-1"></i></div>
                    <div class="flex-grow-1" v-html="page_message.value"></div>
                </div>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">{{ session.name }}</div>
                        <a class="btn btn-primary text-nowrap" :href="'mailto:' + contactEmail + '?subject=Participant question'" ><i class="bi bi-envelope-fill"></i> Contact us about this session</a>
                    </div>
                    <div class="card-body">
                        <h4><b>Description</b></h4>
                        <div v-if="session.description" class="col-lg-9 mb-3 ms-1">{{ session.description }}</div>
                        <div v-else class="col-lg-9 mb-3 ms-1">None</div>
                        <div class="mb-2 ms-1"><b>Days and times:</b>
                            <span v-for="schedule in session.conference_schedules" class="mx-2"><i class="bi bi-calendar-check h6"></i> {{ formatDate(schedule.date + ' ' + schedule.time) }}</span>
                        </div>
                        <div class="mb-2 ms-1"><b>Duration:</b> {{ session.duration_minutes }} minutes</div>
                        <div class="mb-2 ms-1"><b>Type:</b> {{ session.type_name }}</div>
                        <div class="mb-4 ms-1"><b>Track:</b> {{ session.track_name }}</div>
<!--                    <div class="mb-4 ms-1"><b>Track head:</b> </div> -->

                        <h4>Participants</h4>
                        <div v-if="session.session_participants?.length > 1" class="alert alert-info col-lg-10 d-flex my-2 py-2" role="alert">
                            <div><i class="bi bi-exclamation-circle-fill me-1"></i></div>
                            <div class="flex-grow-1">
                                <div>If you would like to share your email with other panelists, you must give permission on <a href="/user/profile">your profile page.</a></div>
                            </div>
                        </div>
                        <div v-for="participant in session.session_participants" class="d-flex border col-lg-6">
                            <div class="p-2 flex-grow-1">
                                {{ participant.badge_name }}
                                <span v-if="participant.is_moderator"><b class="bg-warning  bg-opacity-75 px-1 ms-1"><i class="bi bi-mic-fill small"></i> Moderator</b></span>
                            </div>
                            <div class="p-2" v-if="participant.social_data?.twitter" >
                                <a class="" :href="isValidUrl(participant.social_data.twitter) ? participant.social_data.twitter : null" target="_blank">
                                    <i class="bi bi-twitter h5" data-bs-toggle="tooltip" data-bs-placement="top" :title="participant.social_data.twitter"></i>
                                </a>
                            </div>
                            <div class="p-2" v-if="participant.social_data?.facebook" >
                                <a class="" :href="isValidUrl(participant.social_data.facebook) ? participant.social_data.facebook : null" target="_blank">
                                    <i class="bi bi-facebook h5" data-bs-toggle="tooltip" data-bs-placement="top" :title="participant.social_data.facebook"></i>
                                </a>
                            </div>
                            <div class="p-2" v-if="participant.social_data?.instagram" >
                                <a class="" :href="isValidUrl(participant.social_data.instagram) ? participant.social_data.instagram : null" target="_blank">
                                    <i class="bi bi-instagram h5" data-bs-toggle="tooltip" data-bs-placement="top" :title="participant.social_data.instagram"></i>
                                </a>
                            </div>
                            <div class="p-2" v-if="participant.social_data?.discord" >
                                <a class="" :href="isValidUrl(participant.social_data.discord) ? participant.social_data.discord : null" target="_blank">
                                    <i class="bi bi-discord h5" data-bs-toggle="tooltip" data-bs-placement="top" :title="participant.social_data.discord"></i>
                                </a>
                            </div>
                            <div class="p-2" v-if="participant.website" >
                                <a class="" :href="isValidUrl(participant.website) ? participant.website : null" target="_blank">
                                    <i class="bi bi-globe h5" data-bs-toggle="tooltip" data-bs-placement="top" :title="participant.website"></i>
                                </a>
                            </div>
                            <div class="p-2" v-if="participant.email" >
                                <a class="" :href="'mailto:'+participant.email">
                                    <i class="bi bi-envelope-fill h5" data-bs-toggle="tooltip" data-bs-placement="top" :title="participant.email"></i>
                                </a>
                            </div>
                        </div>

                        <h4 class="mt-4">Discussion prompts and seed questions</h4>
                        <div v-if="session.seed_questions" class="col-lg-9 mb-3 ms-1" style="white-space: pre-wrap">{{ session.seed_questions }}</div>
                        <div v-else class="col-lg-9 mb-3 ms-1" >None</div>

                        <h4 class="mt-2 border-top pt-2">Participant comments and requests</h4>
                        <div v-if="session_comments" class="col-lg-9 mb-3" style="white-space: pre-wrap">
                            <div v-for="comment in session_comments" class="bg-gray-300 p-2 mb-2">
                                {{ comment.comment }}
                                <br>- {{ comment.badge_name }} | {{ formatDate(comment.created_at) }}
                            </div>
                        </div>

                        <label for="new-session-comment" >Do you have a special request or a comment about this session?</label>
                        <div class="mt-1 col-lg-9" >
                            <input type="text" v-model="newSessionComment" id="new-session-comment" class="form-control " placeholder="Add request or comment"  />
                            <button type="button" id="save-comment-button" class="btn btn-primary mt-2" @click="saveComment">Save comment</button>
                        </div>


                    </div>
                    <div class="card-footer text-center"></div>
                </div>
            </div>
        </div>
        <div v-else class="p-5 text-center">
            <div><i class="bi bi-question-diamond-fill" style="font-size: 6em;"></i></div>
            <h1>Error | Not Found</h1>
            Session not found, or you were not found on this session list
        </div>
    </div>

</template>


<script>
    import dayjs from "dayjs";
    import { Tooltip } from "bootstrap";

    export default {
        props: ['userId', 'sessionId', 'conferenceSlug', 'contactEmail'],
        data: function() {
            return {
                session: {
                    participants: []
                },
                session_comments: {},
                user: {},
                page_message: {},
                foundSession: true,
                newSessionComment: '',
            }
        },
        mounted() {
            new Tooltip(document.body, {
                selector: "[data-bs-toggle='tooltip']",
            }),
            this.loadSession();
        },
        methods: {
            formatDate(dateString) {
                if(!dateString) { return null }
                const date = dayjs(dateString);
                return date.format('dddd MMM DD, h:mm A');
            },
            loadSession: function() {
                axios.get(`/api/conference/session/${this.sessionId}`)
                    .then((response) => {
                        this.session = response.data.data;
                        this.user = this.session.session_participants.find(({user_id}) => user_id === this.userId);

                        this.loadMessage();
                        this.loadComments();
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not find the session`);
                        this.foundSession = false
                    });
            },
            loadMessage: function() {
                axios.get('/api/configuration/messsage/message_user_session_view')
                    .then((response) => {
                        this.page_message = response.data.data;
                    })
                    .catch((error) => {

                    })
            },
            loadComments: function() {
                axios.get('/api/session-user-comment', {params : {conference_session_id: this.sessionId}})
                    .then((response) => {
                        this.session_comments = response.data.data;
                    })
                    .catch((error) => {

                    })
            },
            saveComment: function() {
                axios.post('/api/session-user-comment', {conference_session_id: this.sessionId, comment: this.newSessionComment})
                    .then((response) => {
                        this.loadComments();
                        this.newSessionComment = '';
                        this.$toast.success(`New comment saved.`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save the comment.`);
                    })

            },
            isValidUrl: function(string) {
                try {
                    new URL(string);
                    return true;
                } catch (err) {
                    return false;
                }
            }

        }

    }
</script>
