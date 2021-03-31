<template>
    <v-snackbar right :color="color" v-model="snackBar" :timeout="timeout" class="pa-1">{{message}}</v-snackbar>
</template>
<script>
export default {
    props: {
        color: {
            type: String
        },
        message: {
            type: String
        }
    },
    data: () => {
        return {
            snackBar: true,
            timeout: 3000
        }
    }
}
</script>