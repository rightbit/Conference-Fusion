<template>
  <div class="text-center pt-5">
    <div id="presenter-promo">
      <div class="badge-name">
        {{ badgeName }}
      </div>
      <img class="profile-image" :src="profileImage" />
      <img class="conference-image"  :src="conferenceImage" />
    </div>
    <button @click="saveimage" class="btn btn-primary mt-2 me-2">Save as an image</button>
    <a href="/user/profile" class="btn btn-secondary mt-2"><i class="bi bi-person-circle"></i> Update my Profile Picture</a>
  </div>
</template>

<style scoped>

#presenter-promo {
  position: relative;
  width: 800px;
  height: 800px;
}

.conference-image {
  width: 100%;
}

.profile-image {
  width: 40%;
  height: 40%;
  object-fit: cover;
  position: absolute;
  bottom: 20%;
  left: 25%;
  transform: translateX(-25%);
  border-radius: 50%;
  border: 5px solid #977f2f;
}

.badge-name{
  position: absolute;
  bottom: 10%;
  left: 25%;
  transform: translateX(-25%);
  background-color: #977f2f;
  padding: 10px;
  border-radius: 10px;
  width: 40%;
  text-align: center;
  color: white;
  font-size: 1.5rem;
  font-weight: bold;
  text-transform: uppercase;
  text-shadow: 1px 1px 1px #000;
}
</style>
<script>
// TODO - Make this a plugin and add admin features to customize image positions
import * as htmlToImage from 'html-to-image';
import { toJpeg } from 'html-to-image';

export default {
  props: ['badgeName','profileImage', 'conferenceImage'],

  methods: {
    saveimage: function () {
      htmlToImage.toJpeg(document.getElementById('presenter-promo'), { quality: 0.95 })
          .then(function (dataUrl) {
            var link = document.createElement('a');
            link.download = 'presenter-promo.jpg';
            link.href = dataUrl;
            link.click();
      });
    },
  },
}
</script>