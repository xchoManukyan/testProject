<template>
    <div class="my-4">

        <!--header-->
        <v-container fluid class="py-0">
            <v-row>
                <v-col cols="9" md="4">
                    <v-text-field
                        class="my-0 pa-0"
                        v-model="search"
                        append-icon="mdi-magnify"
                        label="Search"
                        single-line
                        hide-details
                        clearable
                        color="primary"
                    ></v-text-field>
                </v-col>
                <v-col cols="3" md="8" class="d-flex justify-end">
                    <v-btn small class="ml-3" fab color="primary" @click="showDialog()">
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>

        <!--data table-->
        <v-data-table
            :search="search"
            fixed-header
            :headers="headers"
            :items="data"
            :loading="loading"
            item-key="id"
            multi-sort
            calculate-widths
        >
            <template v-slot:item.description="{ item }">
                <div style="max-width: 600px;">
                    <small>{{item.description}}</small>
                </div>
            </template>
            <template v-slot:item.appoint="{ item }">
                <div class="d-flex justify-space-between">
                    <span v-if="item.appointed">{{item.appointed.name}}</span>
                    <div v-else style="width: 100%; text-align: center">-</div>
                    <v-menu offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn icon
                                   small
                                   color="primary"
                                   v-bind="attrs"
                                   v-on="on"
                            >
                                <v-icon small>mdi-link-variant</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item-group>
                                <v-list-item
                                    v-for="user in users"
                                    :key="user.id"
                                >
                                    <v-list-item-title @click="appoint(item, user.id)">{{ user.name }}</v-list-item-title>
                                </v-list-item>
                            </v-list-item-group>
                        </v-list>
                    </v-menu>
                </div>
            </template>
            <template v-slot:item.created_at="{ item }">
                <small>{{ item.created_at| dateFormat }}</small>
            </template>
            <template v-slot:item.updated_at="{ item }">
                <small>{{ item.updated_at | dateFormat }}</small>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-btn icon small color="primary" @click="showDialog(item)">
                    <v-icon small>mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon small color="error" @click="showConfirm(item)">
                    <v-icon small>mdi-close</v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <!--dialog-->
        <v-dialog persistent width="600" v-model="dialog">
            <v-card :loading="dialogLoading">
                <v-card-title>
                    <span class="headline">{{dialogItem.id? 'Update': 'Create'}} task</span>
                </v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="dialogItem.name"
                        label="Name"
                        name="name"
                        :error-messages="errors.collect('name')"
                        v-validate="dialogItem.rules.name"
                        data-vv-as="name"
                    />
                    <v-textarea
                        v-model="dialogItem.description"
                        label="Description"
                        name="description"
                        :error-messages="errors.collect('description')"
                        v-validate="dialogItem.rules.description"
                        data-vv-as="description"
                    />
                </v-card-text>
                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="closeDialog()">Cancel</v-btn>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="dialogItem.id? update(): create()"
                        :loading="dialogLoading"
                    >
                        {{dialogItem.id? 'Update': 'Create'}}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!--confirm dialog-->
        <v-dialog persistent width="300" v-model="confirmDialog">
            <v-card v-if="deleteItem" :loading="deleteLoading">
                <v-card-title>
                    <span class="headline">Delete task</span>
                </v-card-title>
                <v-card-text>
                    Are you sure want to delete task: <strong>{{deleteItem.name}}</strong>?
                </v-card-text>
                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="blue darken-1" text @click="closeConfirm()">no</v-btn>
                    <v-btn
                        color="red darken-1"
                        text
                        @click="remove()"
                        :loading="deleteLoading"
                    >
                        yes
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!--snackbar-->
        <v-snackbar bottom right v-model="snackbar" :color="snackbarColor">
            {{snackbarMessage}}
            <template v-slot:action="{ attrs }">
                <v-btn
                    icon
                    color="white"
                    v-bind="attrs"
                    @click="snackbar = false"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </template>
        </v-snackbar>
    </div>
</template>

<script>
import axios from 'axios';
import Task from "../models/Task";
export default {
    props: ['users'],
    name: "Manager",
    data() {
        return {
            loading: false,
            headers: [
                { text: "ID", value: "id" },
                { text: "Name", value: "name" },
                { text: "Description", value: "description"},
                { text: "Status", value: "status.title"},
                { text: "Assigned to", value: "appoint"},
                { text: "Created at", value: "created_at"},
                { text: "Updated at", value: "updated_at"},
                { text: "Actions", value: "actions", sortable: false}
            ],
            data: [],
            search: null,

            dialog: false,
            dialogLoading: false,
            dialogItem: new Task(),

            deleteItem: null,
            deleteLoading: false,
            confirmDialog: false,

            snackbar: false,
            snackbarMessage: null,
            snackbarColor: null
        }
    },
    filters: {
        dateFormat: function (value) {
            if (!value) return ''
            return new Date(value).toISOString().slice(0,16).replace('T', ' ')
        }
    },
    methods: {
        response(message, color){
            this.snackbar = true;
            this.snackbarMessage = message;
            this.snackbarColor = color;
        },

        showDialog(item = {}){
            this.dialogItem = new Task(item);
            this.dialog = true;
        },

        closeDialog(){
            this.dialogItem = new Task();
            this.dialog = false;
        },

        showConfirm(item){
            this.deleteItem = new Task(item);
            this.confirmDialog = true;
        },

        closeConfirm(){
            this.deleteItem = null;
            this.confirmDialog = false;
        },

        getData(){
            this.loading = true;
            axios.get('manager/tasks')
                .then(response => {
                    this.loading = false
                    this.data = response.data
                })
                .catch(error => {
                    this.loading = false;
                    this.response(error.response.data.message, 'error');
                })
        },

        appoint(item, user_id){
            let appoint = new Task(item);
            appoint.appoint(user_id)
                .then(response => {
                    this.getData();
                    this.response(response.data.message, 'success');
                })
                .catch(error => {
                    this.response(error.response.data.message, 'error');
                })
        },

        update(){
            this.$validator.validateAll().then(valid => {
                if (valid){
                    this.dialogLoading = true;
                    this.dialogItem.update()
                        .then(response => {
                            this.getData();
                            this.dialogLoading = false;
                            this.response(response.data.message, 'success');
                            this.closeDialog();
                        })
                        .catch(error => {
                            this.dialogLoading = false;
                            this.response(error.response.data.message, 'error');
                        })
                }
            })
        },

        create(){
            this.$validator.validateAll().then(valid => {
                if (valid){
                    this.dialogLoading = true;
                    this.dialogItem.create()
                        .then(response => {
                            this.getData();
                            this.dialogLoading = false;
                            this.response(response.data.message, 'success');
                            this.closeDialog();
                        })
                        .catch(error => {
                            this.dialogLoading = false;
                            this.response(error.response.data.message, 'error');
                        })
                }
            })
        },

        remove(){
            if (this.deleteItem){
                this.deleteLoading = true;
                this.deleteItem.delete()
                    .then(response => {
                        this.getData();
                        this.deleteLoading = false;
                        this.response(response.data.message, 'success');
                        this.closeConfirm();
                    })
                    .catch(error => {
                        this.deleteLoading = false;
                        this.response(error.response.data.message, 'error');
                    })
            }
        }
    },
    created() {
        this.getData();
    }
}
</script>
