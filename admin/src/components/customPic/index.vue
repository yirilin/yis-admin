<template>
    <span class="headerAvatar">
        <template v-if="picType === 'avatar'">
            <el-avatar :size="30" :src="avatar" v-if="avatar"></el-avatar>
            <el-avatar :size="30" :src="require('@/assets/noBody.png')" v-else></el-avatar>
        </template>
        <template v-if="picType === 'img'">
            <img :src="avatar" class="avatar" v-if="avatar" />
            <img :src="require('@/assets/noBody.png')" class="avatar" v-else/>
        </template>
        <template v-if="picType === 'file'">
            <img :src="file" class="file"/>
        </template>
    </span>
</template>

<script>
const path = process.env.VUE_APP_BASE_API
export default {
    name: "customPic",
    props: {
        picType: {
            type: String,
            required: false,
            default: "avatar"
        },
        picSrc: {
            type: String,
            required: false,
            default: ""
        }
    },
    data(){
        return{
            path: path,
        }
    },
    computed:{
        avatar(){
            if(this.picSrc && this.picSrc.slice(0, 4) !== "http"){
                return this.path + this.picSrc
            }
            return this.picSrc
        },
        file(){
            if(this.picSrc && this.picSrc.slice(0, 4) !== "http"){
                return this.path + this.picSrc
            }
            return this.picSrc
        }
    }
}
</script>

<style scoped>
.headerAvatar{
    display: flex;
    justify-content: center;
    align-items: center;
}
.file{
    width: 80px;
    height: 80px;
    position: relative;
}
</style>