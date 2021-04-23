<template>
  <div>
    <!-- 查询表单开始 -->
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        <el-form-item>
          <el-input v-model="searchInfo.short_name" placeholder="简称" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item>
          <el-input v-model="searchInfo.pinyin" placeholder="拼音" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item>
          <el-input v-model="searchInfo.first" placeholder="首字母" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button @click="onSubmit" type="primary" icon="el-icon-search">查询</el-button>
        </el-form-item>
        <el-form-item>
          <el-button @click="openDialog" type="primary" icon="el-icon-plus">新增</el-button>
        </el-form-item>
        <el-form-item>
          <el-popover placement="top" v-model="deleteVisible" width="160">
            <p>确定要删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button @click="deleteVisible = false" size="mini" type="text">取消</el-button>
              <el-button @click="onDelete" size="mini" type="primary">确定</el-button>
            </div>
            <el-button icon="el-icon-delete" size="mini" slot="reference" type="danger">批量删除</el-button>
          </el-popover>
        </el-form-item>
      </el-form>
    </div>
    <!-- 查询表单结束 -->
    <!-- 列表展示开始 -->
    <el-table :data="tableData" @selection-change="handleSelectionChange" border ref="multipleTable" stripe
              style="width: 100%" tooltip-effect="dark">
      <el-table-column type="selection" width="55"></el-table-column>
      <el-table-column label="简称" prop="short_name" min-width="120"></el-table-column>
      <el-table-column label="名称" prop="name" min-width="120"></el-table-column>
      <el-table-column label="拼音" prop="pinyin" min-width="120"></el-table-column>
      <el-table-column label="长途区号" prop="code" min-width="120"></el-table-column>
      <el-table-column label="邮编" prop="zip_code" min-width="120"></el-table-column>
      <el-table-column label="首字母" prop="first" min-width="120"></el-table-column>
      <el-table-column label="经度" prop="lng" min-width="120"></el-table-column>
      <el-table-column label="纬度" prop="lat" min-width="120"></el-table-column>
      <el-table-column label="操作" width="158">
        <template slot-scope="scope">
          <el-button @click="updateArea(scope.row)" size="small" type="primary" icon="el-icon-edit">编辑</el-button>
          <el-popover placement="top" width="160" v-model="scope.row.visible" style="margin-left: 10px;">
            <p>确定要删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
              <el-button type="primary" size="mini" @click="deleteArea(scope.row)">确定</el-button>
            </div>
            <el-button type="danger" icon="el-icon-delete" size="mini" slot="reference">删除</el-button>
          </el-popover>
        </template>
      </el-table-column>
    </el-table>
    <el-pagination :current-page="page" :page-size="pageSize" :page-sizes="[10, 30, 50, 100]"
                   :style="{float:'right',padding:'20px'}" :total="total" @current-change="handleCurrentChange"
                   @size-change="handleSizeChange" layout="total, sizes, prev, pager, next, jumper"></el-pagination>
    <!-- 列表展示结束 -->
    <!-- 增改表单开始 -->
    <el-dialog :before-close="closeDialog" :visible.sync="dialogFormVisible" :title="dialogTitle" width="480px">
      <el-form ref="elForm" :model="formData" :rules="rules" size="mini" label-width="100px" label-position="left">
        <el-form-item label="父id" prop="pid">
          <el-input v-model="formData.pid" placeholder="请输入父id" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="简称" prop="short_name">
          <el-input v-model="formData.short_name" placeholder="请输入简称" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="名称" prop="name">
          <el-input v-model="formData.name" placeholder="请输入名称" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="全称" prop="merger_name">
          <el-input v-model="formData.merger_name" placeholder="请输入全称" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="层级" prop="level">
          <el-input v-model="formData.level" placeholder="请输入层级省(0)市(1)区(2)县(3)" clearable
                    :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="拼音" prop="pinyin">
          <el-input v-model="formData.pinyin" placeholder="请输入拼音" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="区号" prop="code">
          <el-input v-model="formData.code" placeholder="请输入长途区号" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="邮编" prop="zip_code">
          <el-input v-model="formData.zip_code" placeholder="请输入邮编" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="首字母" prop="first">
          <el-input v-model="formData.first" placeholder="请输入首字母" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="经度" prop="lng">
          <el-input v-model="formData.lng" placeholder="请输入经度" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="纬度" prop="lat">
          <el-input v-model="formData.lat" placeholder="请输入纬度" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>

      </el-form>
      <div class="dialog-footer" slot="footer">
        <el-button @click="closeDialog">取消</el-button>
        <el-button @click="enterDialog" type="primary">确定</el-button>
      </div>
    </el-dialog>
    <!-- 增改表单结束 -->
  </div>
</template>

<script>
import {
  createArea,
  updateArea,
  findArea,
  getAreaList,
  deleteArea
} from "@/api/area";
import {formatTimeToStr} from "@/utils/data";
import infoList from "@/components/mixins/infoList";

export default {
  name: "Area",
  mixins: [infoList],
  data() {
    return {
      listApi: getAreaList,
      dialogFormVisible: false,
      dialogTitle: "新增地区",
      visible: false,
      type: "",
      deleteVisible: false,
      multipleSelection: [],
      formData: {
        pid: null, short_name: null, name: null, merger_name: null, level: null,
        pinyin: null, code: null, zip_code: null, first: null, lng: null, lat: null,
      },
      rules: {
        pid: [{required: true, message: "请输入父id", trigger: "blur",}],
        short_name: [{required: true, message: "请输入简称", trigger: "blur",}],
        name: [{required: true, message: "请输入名称", trigger: "blur",}],
        merger_name: [{required: true, message: "请输入全称", trigger: "blur",}],
        level: [{required: true, message: "请输入层级 0 1 2 省市区县", trigger: "blur",}],
        pinyin: [{required: true, message: "请输入拼音", trigger: "blur",}],
        code: [{required: true, message: "请输入长途区号", trigger: "blur",}],
        zip_code: [{required: true, message: "请输入邮编", trigger: "blur",}],
        first: [{required: true, message: "请输入首字母", trigger: "blur",}],
        lng: [{required: true, message: "请输入经度", trigger: "blur",}],
        lat: [{required: true, message: "请输入纬度", trigger: "blur",}],
      },
    };
  },
  filters: {
    formatDate: function (time) {
      if (time != null && time != "") {
        var date = new Date(time);
        return formatTimeToStr(date, "yyyy-MM-dd hh:mm:ss");
      } else {
        return "";
      }
    },
    formatBoolean: function (bool) {
      if (bool != null) {
        return bool ? "是" : "否";
      } else {
        return "";
      }
    },
  },
  methods: {
    //条件搜索前端看此方法
    onSubmit() {
      this.page = 1
      this.pageSize = 10
      this.getTableData()
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    async onDelete() {
      const ids = []
      this.multipleSelection &&
      this.multipleSelection.map(item => {
        ids.push(item.id)
      })
      const res = await deleteArea(JSON.stringify(ids))
      if (res.code == 200) {
        this.$message({
          type: 'success',
          message: '批量删除成功'
        })
        this.deleteVisible = false
        this.getTableData()
      }
    },
    async updateArea(row) {
      const res = await findArea(row.id);
      this.type = "update";
      if (res.code == 200) {
        this.formData = res.data;
        this.dialogTitle = "编辑地区";
        this.dialogFormVisible = true;
      }
    },
    closeDialog() {
      this.dialogFormVisible = false;
      this.formData = {
        pid: null,
        short_name: null,
        name: null,
        merger_name: null,
        level: null,
        pinyin: null,
        code: null,
        zip_code: null,
        first: null,
        lng: null,
        lat: null,
      };
    },
    async deleteArea(row) {
      this.visible = false;
      const res = await deleteArea(row.id);
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "删除成功"
        });
        this.getTableData();
      }
    },
    async enterDialog() {
      let res;
      switch (this.type) {
        case "create":
          res = await createArea(this.formData);
          break;
        case "update":
          res = await updateArea(this.formData.id, this.formData);
          break;
        default:
          res = await createArea(this.formData);
          break;
      }
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "操作成功"
        })
        this.closeDialog();
        this.getTableData();
      }
    },
    openDialog() {
      this.type = "create";
      this.dialogTitle = "新增地区";
      this.dialogFormVisible = true;
    }
  },
  async created() {
    await this.getTableData();
  }
};
</script>

<style>
</style>