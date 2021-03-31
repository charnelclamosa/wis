<template>
    <v-btn tile outlined color="error" :disabled="disabled">{{text}}</v-btn>
</template>
<script>
export default {
    props: {
        text: {
            type: String
        },
        disabled: {
            type: Boolean
        }
    }
}
</script>