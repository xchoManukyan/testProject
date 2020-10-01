<template>
    <div>
        <v-app-bar
            color="primary"
            dense
            dark
        >
            <v-toolbar-title>{{title}}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="logout()">
                <v-icon>mdi-logout</v-icon>
            </v-btn>
        </v-app-bar>
    </div>
</template>

<script>
    export default {
        props: ['title', 'csrf'],
        name: "DashboardHeader",
        methods: {
            logout(){
                axios.post('/logout', {'_token': this.csrf}).then(()=>{
                    document.location.reload(true)
                });
            }
        }
    }
</script>
