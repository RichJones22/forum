<template>
    <div>
        <div v-if="signedIn">
            <textarea name="body"
                  id="body"
                  class="form-control"
                  placeholder="Have something to say?"
                  rows="5"
                  required
                  v-model="body"></textarea>

            <button type="submit"
                    class="btn btn-default"
                    style="margin-top: 5px;"
                    @click="addReply">Post</button>
        </div>

        <p class="text-center" v-else>
                Please <a href="/login">sign in</a> to participate in this discussion
        </p>
    </div>
</template>

<script>
    export default {
        props: ['endpoint'],
        data() {
            return {
                body: '',
                // endpoint: this.endpoint
            }
        },
        computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },
        methods: {
            addReply() {
                console.log('--addReply--');

                axios.post(this.endpoint, { body: this.body})
                    .then(({data}) => {
                        this.body = '';

                        flash('Your reply has been posted...');

                        this.$emit('created', data);
                    });
            }
        }
    }
</script>

<style>

</style>