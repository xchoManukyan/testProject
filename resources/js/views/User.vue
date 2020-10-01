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
            <template v-slot:item.status="{ item }">
                <div class="d-flex justify-space-between">
                    <span>{{item.status.title}}</span>
                    <v-menu v-if="user.id === item.appoint_id" offset-y>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn icon
                                   small
                                   color="primary"
                                   v-bind="attrs"
                                   v-on="on"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-item-group>
                                <v-list-item
                                    v-for="status in statuses"
                                    :key="status.id"
                                >
                                    <v-list-item-title @click="changeStatus(item, status.id)">{{ status.title }}</v-list-item-title>
                                </v-list-item>
                            </v-list-item-group>
                        </v-list>
                    </v-menu>
                </div>
            </template>
            <template v-slot:item.appoint="{ item }">
                <span v-if="item.appointed">{{item.appointed.name}}</span>
                <div v-else style="width: 100%; text-align: center">-</div>
            </template>
            <template v-slot:item.created_at="{ item }">
                <small>{{ item.created_at| dateFormat }}</small>
            </template>
            <template v-slot:item.updated_at="{ item }">
                <small>{{ item.updated_at | dateFormat }}</small>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-btn icon small color="primary" @click="showItemPage(item.id)">
                    <v-icon small>mdi-eye</v-icon>
                </v-btn>
            </template>
        </v-data-table>
    </div>
</template>
<script>
import axios from 'axios';
import Task from "../models/Task";
export default {
    props: ['statuses', 'user'],
    name: "User",
    data() {
        return {
            loading: false,
            headers: [
                { text: "ID", value: "id" },
                { text: "Name", value: "name" },
                { text: "Description", value: "description"},
                { text: "Status", value: "status"},
                { text: "Assigned to", value: "appoint"},
                { text: "Created at", value: "created_at"},
                { text: "Updated at", value: "updated_at"},
                { text: "Actions", value: "actions", sortable: false}
            ],
            data: [],
            search: null,
        }
    },
    filters: {
        dateFormat: function (value) {
            if (!value) return ''
            return new Date(value).toISOString().slice(0,16).replace('T', ' ')
        }
    },
    methods: {
        response(message, color) {
            this.snackbar = true;
            this.snackbarMessage = message;
            this.snackbarColor = color;
        },

        getData(){
            this.loading = true;
            axios.get('user/tasks')
                .then(response => {
                    this.loading = false
                    this.data = response.data
                })
                .catch(error => {
                    this.loading = false;
                    this.response(error.response.data.message, 'error');
                })
        },

        changeStatus(item, status_id){
            let changeItem = new Task(item);
            changeItem.changeStatus(status_id)
                .then(response => {
                    this.getData();
                    this.response(response.data.message, 'success');
                })
                .catch(error => {
                    this.response(error.response.data.message, 'error');
                })
        },

        showItemPage(id){
            window.location = window.location.pathname + '/'+id
        }
    },
    created() {
        this.getData();
    }
}
</script>

