<template>
  <div class="text-center pt-5">
    <div id="presenter-promo">
      <div ref="badgeName" class="badge-name">
        <span ref="badgeText">{{ badgeName }}</span>
      </div>
        <img class="conference-image"  :src="conferenceImage" />
        <img class="profile-image" :src="profileImage" />
    </div>
    <button @click="saveimage" class="btn btn-primary mt-2 me-2">Save as an image</button>
    <a href="/user/profile" class="btn btn-secondary mt-2"><i class="bi bi-person-circle"></i> Update my Profile Picture</a>
  </div>
</template>

<style scoped>

#presenter-promo {
  position: relative;
  width: 100%;
  text-align:center;
}

.conference-image {
  position: relative;
  z-index: 100;
  width: 100%;
}

.profile-image {
  width: 43%;
  object-fit: cover;
  position: absolute;
  bottom: 44.66%;
  left: 35%;
  transform: translateX(-51%);
  z-index: 10;
}

.badge-name{
  position: absolute;
  z-index: 110;
  top: 45%;
  left: 77%;
  transform: translateX(-50%);
  padding: 10px;
  border-radius: 10px;
  width: 40%;
  text-align: center;
  color: #a92e36;
  font-size: 5rem;
  font-family: 'Cormorant Garamond', serif;
  font-weight: bold;
  white-space: normal;
  word-break: normal;
  overflow-wrap: normal;
  hyphens: none;
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

  mounted() {
    this.badgeResizeObserver = new ResizeObserver(this.fitBadgeName);
    this.badgeResizeObserver.observe(this.$refs.badgeName.parentElement);
    window.addEventListener('resize', this.fitBadgeName);
    document.fonts.addEventListener('loadingdone', this.fitBadgeName);
    document.fonts.ready.then(this.fitBadgeName);
    this.fitBadgeName();
  },

  beforeUnmount() {
    this.badgeResizeObserver.disconnect();
    window.removeEventListener('resize', this.fitBadgeName);
    document.fonts.removeEventListener('loadingdone', this.fitBadgeName);
  },

  watch: {
    badgeName() {
      this.$nextTick(this.fitBadgeName);
    },
  },

  methods: {
    fitBadgeName() {
      const badge = this.$refs.badgeName;
      const text = this.$refs.badgeText;
      if (!badge || !text || !badge.clientWidth) {
        return;
      }

      badge.style.removeProperty('font-size');
      const style = window.getComputedStyle(badge);
      const availableWidth = badge.clientWidth
          - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
      const fits = () => Array.from(text.getClientRects())
          .every(rect => rect.width <= availableWidth);

      if (availableWidth <= 0 || fits()) {
        return;
      }

      // Keep the responsive font size unless an unbroken word overflows.
      let minimum = 0;
      let maximum = parseFloat(style.fontSize);
      while (maximum - minimum > 0.1) {
        const size = (minimum + maximum) / 2;
        badge.style.fontSize = `${size}px`;
        if (fits()) {
          minimum = size;
        } else {
          maximum = size;
        }
      }
      badge.style.fontSize = `${minimum}px`;
    },

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
