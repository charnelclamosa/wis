<template>
    <v-btn tile block color="primary">{{text}}</v-btn>
</template>
<script>
export default {
    props: {
        text: {
            type: String
        }
    }
}
</script>