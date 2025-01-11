<template>
    <div class="modal" id="updatePasswordModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeUpdatePasswordModal"></button>
                </div>
                <div class="modal-body">
                  <div class="row g-2 mb-3">
                    <div class="col-md-4">
                      <label for="password">Password</label>
                    </div>
                    <div class="col-md-8">
                      <input v-model="password" type="password">
                    </div>
                    <div class="col-md-4">
                      <label for="password">Confirm Password</label>
                    </div>
                    <div class="col-md-8">
                      <input v-model="confirmPassword" type="password">
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" v-on:click="updatePassword">Update Password</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['userId'],
    data: function() {
        return {
            password: '',
            confirmPassword: '',
        }
    },
    methods: {
        updatePassword: function(event) {
          if(!this.password) {
            this.$toast.error(`Please enter a password`);
            return
          }

          if(this.password !== this.confirmPassword) {
            this.$toast.error(`Password fields do not match`);
            return
          }

          if(this.password.length < 5) {
            this.$toast.error(`Please use at least 6 characters`);
            return
          }

          axios.post(`/api/profile/user/password/${this.userId}`, {password: this.password})
              .then((response) => {
                this.$toast.success(`Updated the password successfully`);
                document.getElementById('closeUpdatePasswordModal').click();
              })
              .catch((error) => {
                if(error.response && error.response.status == 403) {
                  this.$toast.error(`You do not have permission to update this user`);
                  return;
                }
                this.$toast.error(`Could not save password`);
              });


        },
    }
};
</script>
