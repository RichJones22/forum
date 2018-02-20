<script>
    import Replies from '../components/replies.vue';

    export default {
        props: ['initRepliesCount'],
        components: {Replies},

        data() {
            return {
                repliesCount: this.initRepliesCount
            }
        }
    }
</script>