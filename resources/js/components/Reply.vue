<template>
    <div :id="'reply-' + id" class="card mb-3">
            <div class="card-header">
                <div class="level">
                    <h5 class="flex">
                        <a :href="'/profiles/' + data.owner.name" v-text="data.owner.name"></a>
                        said <span v-text="ago"></span>
                    </h5>
                    <div v-if="signedIn">
                        <favorite :reply="data"></favorite>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="editing">
                    <div class="form-group">
                        <textarea name="" class="form-control" v-model="body"></textarea>
                    </div>

                    <button class="btn btn-sm btn-primary" @click="update">Update</button>
                    <button class="btn btn-sm btn-link" @click="editing=false">Cancel</button>
                </div>
                <div v-else v-text="body"></div>
            </div>
            <div class="card-footer level" v-if="canUpdate">

                <button class="btn btn-outline-secondary btn-sm mr-2" @click="editing=true">Edit</button>
                <button class="btn btn-danger btn-sm mr-2" @click="destroy">Delete</button>
            </div>
        </div>
</template>
<script>

    import Favorite from './Favorite';
    import moment from 'moment';

    export default {
        props: ['data'],

        components: { Favorite },

        data () {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body
            }
        },
        computed: {
            ago () {
                return moment(this.data.created_at).fromNow();
            },
            signedIn () {
                return window.App.signedIn;
            },
            canUpdate () {
                return this.authorize(user => this.data.user_id == user.id);
            }
        },
        methods: {
            update () {
                axios.patch(
                    '/replies/' + this.data.id, {
                    body: this.body
                    })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    });

                this.editing = false;

                flash('Your reply has been updated!');
            },

            destroy () {
                axios.delete('/replies/' + this.data.id);

                this.$emit('deleted', this.data.id);
            }
        }
    }
</script>