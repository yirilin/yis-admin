<template>
  <div>
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        <el-form-item>
          <el-input placeholder="标题" clearable v-model="searchInfo.title"></el-input>
        </el-form-item>
        <el-form-item>
          <el-input placeholder="名称" clearable v-model="searchInfo.name"></el-input>
        </el-form-item>
        <el-form-item prop="status">
          <el-select v-model="searchInfo.status" clear placeholder="状态">
            <el-option key="" label="全部" value=""></el-option>
            <el-option key="true" label="启用" value="1"></el-option>
            <el-option key="false" label="停用" value="0"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button @click="onSubmit" type="primary" icon="el-icon-search">查询</el-button>
        </el-form-item>
        <el-form-item>
          <el-button @click="openDialog" type="primary" icon="el-icon-plus">新增</el-button>
        </el-form-item>
      </el-form>
    </div>
    <el-table :data="tableData" border ref="multipleTable" stripe style="width: 100%" tooltip-effect="dark">
      <el-table-column label="标题" prop="title" min-width="120"></el-table-column>
      <el-table-column label="名称" prop="name" min-width="120"></el-table-column>
      <el-table-column label="状态" prop="status" min-width="120">
        <template slot-scope="scope">{{ scope.row.status | formatBoolean }}</template>
      </el-table-column>
      <el-table-column label="操作时间" prop="updated_at" width="158">
        <template slot-scope="scope">{{ scope.row.updated_at | formatDate }}</template>
      </el-table-column>
      <el-table-column label="操作" width="230">
        <template slot-scope="scope">
          <el-button @click="toDetile(scope.row)" size="small" type="success" icon="el-icon-view">详情</el-button>
          <el-button @click="updateConfig(scope.row)" size="small" type="primary" icon="el-icon-edit">编辑</el-button>
          <el-popover placement="top" width="160" v-model="scope.row.visible" style="margin-left: 10px">
            <p>确定要删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
              <el-button type="primary" size="mini" @click="deleteConfig(scope.row)">确定</el-button>
            </div>
            <el-button type="danger" icon="el-icon-delete" size="mini" slot="reference">删除</el-button>
          </el-popover>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination :current-page="page" :page-size="pageSize" :page-sizes="[10, 30, 50, 100]"
                   :style="{ float: 'right', padding: '20px' }"
                   :total="total"
                   @current-change="handleCurrentChange"
                   @size-change="handleSizeChange"
                   layout="total, sizes, prev, pager, next, jumper"></el-pagination>
    <el-dialog :before-close="closeDialog" :visible.sync="dialogFormVisible" :title="dialogTitle" width="480px">
      <el-form ref="elForm" :model="formData" :rules="rules" size="medium" label-width="110px">
        <el-form-item label="标题" prop="title">
          <el-input v-model="formData.title" placeholder="请输入标题" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="名称" prop="name">
          <el-input v-model="formData.name" placeholder="请输入名称" clearable :style="{ width: '100%' }"></el-input>
        </el-form-item>
        <el-form-item label="状态" prop="status" required>
          <el-switch v-model="formData.status" active-text="开启" inactive-text="停用"></el-switch>
        </el-form-item>
      </el-form>
      <div class="dialog-footer" slot="footer">
        <el-button @click="closeDialog">取消</el-button>
        <el-button @click="enterDialog" type="primary">确定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  createConfig,
  deleteConfig,
  updateConfig,
  findConfig,
  getConfigList,
} from "@/api/config";
import {formatTimeToStr} from "@/utils/data";
import infoList from "@/components/mixins/infoList";

export default {
  name: "Config",
  mixins: [infoList],
  data() {
    return {
      listApi: getConfigList,
      dialogFormVisible: false,
      dialogTitle: "新增键值",
      visible: false,
      type: "",
      formData: {
        name: null,
        title: null,
        status: true,
      },
      rules: {
        title: [
          {
            required: true,
            message: "请输入标题",
            trigger: "blur",
          },
        ],
        name: [
          {
            required: true,
            message: "请输入名称",
            trigger: "blur",
          },
        ],
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
        return bool ? "启用" : "停用";
      } else {
        return "";
      }
    },
  },
  methods: {
    toDetile(row) {
      this.$router.push({
        name: "configValue",
        params: {
          id: row.id,
        },
      });
    },
    onSubmit() {
      this.page = 1;
      this.pageSize = 10;
      this.getTableData();
    },
    async updateConfig(row) {
      const res = await findConfig(row.id);
      this.type = "update";
      if (res.code == 200) {
        this.formData = res.data.configValue;
        this.formData.status = this.formData.status == 1;
        this.dialogTitle = "编辑键值";
        this.dialogFormVisible = true;
      }
    },
    closeDialog() {
      this.dialogFormVisible = false;
      this.formData = {
        name: null,
        type: null,
        status: true,
        desc: null,
      };
    },
    async deleteConfig(row) {
      this.visible = false;
      const res = await deleteConfig(row.id);
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "删除成功",
        });
        this.page = 1;
        await this.getTableData();
      }
    },
    async enterDialog() {
      this.$refs["elForm"].validate(async (valid) => {
        if (!valid) return;
        let res;
        switch (this.type) {
          case "create":
            res = await createConfig(this.formData);
            break;
          case "update":
            res = await updateConfig(this.formData.id, this.formData);
            break;
          default:
            res = await createConfig(this.formData);
            break;
        }
        if (res.code == 200) {
          this.$message({
            type: "success",
            message: "操作成功!",
          });
          this.closeDialog();
          await this.getTableData();
        }
      });
    },
    openDialog() {
      this.type = "create";
      this.dialogTitle = "新增键值";
      this.dialogFormVisible = true;
    },
  },
  async created() {
    await this.getTableData();
  },
};
</script>

<style>
</style>