<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col">
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between">
            <div class="h4 align-self-center mb-lg-0">Sponsor list for {{ this.conferenceName }}</div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <div>
                Found {{ totalSponsors }} sponsors
              </div>
            </div>
            <table class="table table-sm fs-90">
              <thead>
              <tr>
                <th class="ps-2 w-25">Name</th>
                <th class="w-25">Description</th>
                <th class="w-auto">Image</th>
                <th class="w-25">Link</th>
                <th style="width:5%">Display Order</th>
                <th v-if="this.canEdit"></th>
              </tr>
              </thead>
              <tbody>
              <tr scope="row" v-for="sponsor in sponsors">
                <td class="ps-2"><input type="text" class="form-control form-control-sm" v-model="sponsor.name" :disabled="!this.canEdit" required /></td>
                <td><textarea class="form-control form-control-sm" v-model="sponsor.description" :disabled="!this.canEdit"></textarea></td>
                <td>
                  <img v-if="sponsor.sponsor_image" :src="sponsor.sponsor_image" style="width: 75px" data-bs-toggle="modal" data-bs-target="#upload-image-modal" v-on:click="setModalSponsor(sponsor)">
                  <button v-else type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#upload-image-modal" v-on:click="setModalSponsor(sponsor)" >Upload Image</button>
                </td>
                <td><input type="text" class="form-control form-control-sm" v-model="sponsor.link" :disabled="!this.canEdit" /></td>
                <td><input type="text" class="form-control form-control-sm" v-model="sponsor.display_order" :disabled="!this.canEdit" /></td>
                <td v-if="this.canEdit">
                  <button @click="updateSponsor(sponsor)" type="button" class="btn btn-primary btn-sm me-1 my-1"><i class="bi bi-pencil-square"></i></button>
                  <button @click="deleteSponsor(sponsor)" type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-center">
            <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadSponsors" />
          </div>
        </div>
        <div class="card mb-3" v-if="this.canEdit">
          <div class="card-body" id="addinfo">
            <h5 class="card-title">Add New Sponsor</h5>
            <form @submit.prevent="addSponsor">
              <div class="p-1">
                <label for="title" class="form-label">Name</label>
                <input type="text" class="form-control" id="title" v-model="new_sponsor.name" required>
              </div>
              <div class="p-1">
                <label for="content" class="form-label">Description</label>
                <span class="form-text small ms-2">(Leave blank if name is enough.)</span>
                <textarea class="form-control" id="content" v-model="new_sponsor.description"></textarea>
              </div>
              <div class="p-1">
                <label for="content" class="form-label">Link</label>
                <span class="form-text small ms-2">(include https://)</span>
                <input type="text" class="form-control" id="title" v-model="new_sponsor.link">
              </div>
              <div class="p-1">
                <label for="content" class="form-label">Display Order</label>
                <input type="text" class="form-control" id="title" v-model="new_sponsor.display_order">
              </div>
              <div class="p-1 text-end">
                <button type="submit" class="btn btn-primary">Add Sponsor</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="modal" id="upload-image-modal" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update sponsor image</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" v-on:click="clearImageForm"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex align-items-center justify-content-center text-center">
            <label class="btn btn-success" for="sponsorImageFile">Select file</label>
            <input type="file" id="sponsorImageFile" class="visually-hidden" accept="image/png, image/jpeg" v-on:change="selectFile" />
            <input type="text" id="sponsorImageFileName" class="form-control ms-1" style="width:50%" disabled v-model="sponsorImageName" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" v-on:click="clearImageForm">Cancel</button>
          <button type="button" class="btn btn-primary" v-on:click="submitFile" data-bs-dismiss="modal">Upload image</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
    props: ['conferenceId', 'conferenceName', 'canEdit'],
    data: function() {
        return {
            sponsors: [],
            totalSponsors: '0',
            keyword: '',
            laravelData: {},
            modalSponsor: {},
            sponsorImage: '',
            sponsorImageName: '',
            new_sponsor: {
                name: '',
                descritpion: '',
                link: '',
                display_order: '99',
            },
        }
    },
    mounted() {
        this.loadSponsors();
    },
    methods: {
        loadSponsors: function (page = 1) {
            axios.get('/api/admin/sponsor', {
                    params: {
                        conference_id: this.conferenceId,
                        page: page
                    }
                })
                .then((response) => {
                    this.totalSponsors = response.data.meta.total;
                    this.sponsors = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the sponsors`);
                });
        },
        addSponsor: function() {
            this.new_sponsor.conference_id = this.conferenceId;
            axios.post('/api/admin/sponsor', this.new_sponsor)
                .then((response) => {
                    this.$toast.success(`New sponsor added`);
                    this.loadSponsors();
                    this.new_sponsor.name = '';
                    this.new_sponsor.website = '';
                    this.new_sponsor.description = '';
                    this.new_sponsor.display_order = '';
                })
                .catch((error) => {
                    this.$toast.error(`Could not add the sponsor`);
                });
        },
        deleteSponsor: function(sponsor) {
            axios.delete(`/api/admin/sponsor/${sponsor.id}`)
                .then((response) => {
                    this.$toast.success(`Sponsor deleted`);
                    this.loadSponsors();
                })
                .catch((error) => {
                    this.$toast.error(`Could not delete the sponsor`);
                });
        },
        updateSponsor: function(sponsor) {
          axios.put(`/api/admin/sponsor/${sponsor.id}`, sponsor)
              .then((response) => {
                this.$toast.success(`Sponsor updated`);
              })
              .catch((error) => {
                this.$toast.error(`Could not update the sponsor`);
              });
        },
        selectFile: function(event) {
          var imageHandler = event.target.files[0];
          if( imageHandler.type === 'image/jpeg' || imageHandler.type === 'image/png') {
            this.sponsorImage = imageHandler;
            this.sponsorImageName = imageHandler.name;
            return null;
          }
          this.$toast.error(`Please upload only a jpg or png file`);
        },
        submitFile: function() {
          let formData = new FormData();
          formData.append('file', this.sponsorImage);
          axios.post(`/api/admin/sponsor-image/${this.modalSponsor.id}`, formData,
              {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              })
              .then((response) => {
                this.modalSponsor = {};
                this.clearImageForm();
                this.loadSponsors();
                this.$toast.success(`The sponsor image was successfully updated`);
              })
              .catch((error) => {
                this.$toast.error(`Could not save sponsor image<br />` + error.response.data.message);
              });
        },
        clearImageForm  : function() {
          this.sponsorImage = '';
          this.sponsorImageName = '';
        },
        setModalSponsor: function(sponsor) {
          this.modalSponsor = sponsor;
          console.log(this.modalSponsor);
        },
    }
}
</script>