<template>
  <div class="text-center pt-5">
    <div id="presenter-promo">
      <div class="badge-name">
        M. Todd Gallowglas
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
  width: 100%;
  text-align:center
}

.conference-image {
  width: 100%;
}

.profile-image {
  width: 42%;
  height: 34%;
  object-fit: cover;
  position: absolute;
  bottom: 39.2%;
  left: 51%;
  transform: translateX(-51%);
  border-radius: 50%;
}

.badge-name{
  position: absolute;
  top: 68%;
  left: 50%;
  transform: translateX(-50%);
  padding: 10px;
  border-radius: 10px;
  width: 85%;
  text-align: center;
  color: white;
  font-size: 4.5rem;
  font-family: 'Exo', sans-serif;
  font-weight: bold;
  text-shadow: 1px 1px 1px #000;
  line-height: .8em;
}


@media (max-width: 1399px) {
  .badge-name {
    font-size: 3rem; /* Large screens */
  }
}

@media (max-width: 990px) {
  .badge-name {
    font-size: 2.2rem; /* Medium screens */
  }
}


@media (max-width: 799px) {
  .badge-name {
    font-size: 2.2rem; /* Small screens */
  }
}

@media (max-width: 599px) {
  .badge-name {
    font-size: 2rem; /* Mobile screens */
  }
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