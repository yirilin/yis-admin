<template>
  <div>
    <el-upload class="image-uploader"
      :action="`${path}/fileUploadAndDownload/upload`"
      :show-file-list="false"
      :on-success="handleImageSuccess"
      :before-upload="beforeImageUpload"
      :multiple="false">
      <img v-if="imageUrl" :src="imageUrl" class="image" />
      <i v-else class="el-icon-plus image-uploader-icon"></i>
    </el-upload>
  </div>
</template>
<script>
const path = process.env.VUE_APP_BASE_API;
import ImageCompress from "@/utils/image.js";
export default {
  name: "upload-image",
  model: {
    prop: "imageUrl",
    event: "change",
  },
  props: {
    imageUrl: {
      type: String,
      default: "",
    },
    fileSize: {
      type: Number,
      default: 2048,//2M超出后执行压缩
    },
    maxWH: {
      type: Number,
      default: 1920,//图片长宽上限
    },
  },
  data() {
    return {
      path: path,
    };
  },
  methods: {
    beforeImageUpload(file) {
      let isRightSize = file.size / 1024 < this.fileSize;
      if (!isRightSize) {
        // 压缩
        let compress = new ImageCompress(file, this.fileSize, this.maxWH);
        return compress.compress();
      }
      return isRightSize;
    },
    handleImageSuccess(res) {
      const {  data } = res;
      if (data.file) {
        this.$emit("change", data.file.url);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.image-uploader {
  border: 1px dashed #d9d9d9;
  width: 180px;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.image-uploader {
  border-color: #409eff;
}
.image-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 178px;
  height: 178px;
  line-height: 178px;
  text-align: center;
}
.image {
  width: 178px;
  height: 178px;
  display: block;
}
</style>