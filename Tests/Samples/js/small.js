const iconPlayClass = 'icon icon-play';

export default {
  props: {
    /** @property {string} uuid */
    asset: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      duration: 0,
      progressStyle: 'width: 0%',
      playbackButtonClass: iconPlayClass,
    };
  },
  mounted() {
    if (!this.$refs.player.canPlayType) {
      // @todo error handling
    }
  },
};