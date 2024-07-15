<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="text-end"><a href="/home"><i class="bi bi-arrow-left-circle"></i> Back home</a></div>
                <div class="card mb-3">
                    <div :class="'card-header d-flex justify-content-between'">
                        <div class="h4 align-self-center mb-lg-0">Participate in the mass signing</div>

                    </div>
                    <div class="card-body" id="addinfo">
                        <form id="signing" class="row g-3 align-items-center" @submit.prevent="updateUserInfoConsignment">
                            <div class="d-flex flex-wrap">
                              <div>
                                <label class="me-2">I would like to participate in the mass signing event Friday night:</label>
                              </div>
                              <div class="w-auto">
                                <select id="partipating" class="form-select form-select-sm" v-model.number="userInfoConsignment.participating">
                                  <option value="0">No</option>
                                  <option value="1">Yes</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-12 mb-3">
                                If you would like to request a book partner to possibly carry your books, please enter the information below. (Optional)
                            </div>
                            <div class="row mb-0">
                                <label for="book1title">Book 1</label>
                            </div>
                            <div class="row g-2  mb-2">
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book1_title" placeholder="Title" />
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book1_author" placeholder="Author" />
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book1_isbn" placeholder="ISBN" />
                                </div>
                            </div>
                            <div class="row mb-0">
                                <label for="book1title">Book 2</label>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book2_title"  placeholder="Title" />
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book2_author" placeholder="Author" />
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book2_isbn" placeholder="ISBN" />
                                </div>
                            </div>
                            <div class="row mb-0">
                                <label for="book1title">Book 3</label>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book3_title"  placeholder="Title" />
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book3_author" placeholder="Author" />
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" v-model="userInfoConsignment.book3_isbn" placeholder="ISBN" />
                                </div>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary " ><i class="bi bi-arrow-up-circle me-2"></i>Submit</button>
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
    props: ['conferenceId'],
    data: function() {
        return {
            userInfoConsignment: {},
        }
    },
    mounted() {
        this.loadInfo();
    },
    methods: {
        loadInfo: function () {
            axios.get('/api/profile/user-info-consignment/')
                .then((response) => {
                    this.userInfoConsignment = response.data.data;

                })
                .catch((error) => {
                    this.$toast.error(`Could not load the signing info`);
                });
        },
        updateUserInfoConsignment: function() {
            if(this.userInfoConsignment.id) {
                axios.put(`/api/profile/user-info-consignment/${this.userInfoConsignment.id}`, this.userInfoConsignment )
                    .then((response) => {
                        this.$toast.success(`Updated the user successfully`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save the user info<br />` + error.response.data.message);
                    });
            } else {
                axios.post('/api/profile/user-info-consignment', this.userInfoConsignment )
                    .then((response) => {
                        //this.userInfoConsignment = response.data.data;
                        this.$toast.success(`Saved your mass signing preferences`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save preferences`);
                    });
            }
        }
    }
}
</script>

