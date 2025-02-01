<template>
    <div class="modal" id="profileModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update profile image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>We recommend a square image, and at least 300px wide.</p>
                    <div class="d-flex align-items-center justify-content-center text-center">
                        <label class="btn btn-success" for="profileFile">Select file</label>
                        <input type="file" id="profileFile" class="visually-hidden" accept="image/png, image/jpeg" v-on:change="selectFile" />
                        <input type="text" id="profileFileName" class="form-control ms-1" style="width:50%" disabled v-model="profileFileName" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" v-on:click="submitFile" data-bs-dismiss="modal">Upload image</button>
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
            profileFileName: '',
            profileImage: '',
        }
    },
    emits: ['changeImage'],
    methods: {
        selectFile: function(event) {
            var imageHandler = event.target.files[0];
            if( imageHandler.type === 'image/jpeg' || imageHandler.type === 'image/png') {
                this.profileImage = imageHandler;
                this.profileFileName = imageHandler.name;
                return null;
            }
            this.$toast.error(`Please upload only a jpg or png file`);
        },
        submitFile: function() {
            let formData = new FormData();
            formData.append('file', this.profileImage);
            axios.post(`/api/profile-image/${this.userId}`, formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then((response) => {
                    this.profileImage = '';
                    this.profileFileName ='';
                    this.$toast.success(`The profile image was successfully updated`);
                    this.$emit("changeImage", response.data.data.info.profile_image);
                })
                .catch((error) => {
                    this.$toast.error(`Could not save profile image<br />` + error.response.data.message);
                });
        },
    }
};
</script>
