<template>
  <div>
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        <el-form-item>
          <el-input placeholder="展示值" clearable v-model="searchInfo.label"></el-input>
        </el-form-item>
        <el-form-item>
          <el-input placeholder="配置值" clearable v-model="searchInfo.value"></el-input>
        </el-form-item>
        <el-form-item prop="status">
          <el-select v-model="searchInfo.status" placeholder="状态">
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
      <el-table-column label="展示值" prop="label" min-width="120"></el-table-column>
      <el-table-column label="配置值" prop="value" min-width="120"></el-table-column>
      <el-table-column label="启用状态" prop="status" min-width="120">
        <template slot-scope="scope">{{ scope.row.status|formatBoolean }}</template>
      </el-table-column>
      <el-table-column label="排序" prop="sort" min-width="120"></el-table-column>
      <el-table-column label="操作时间" prop="updated_at" width="158">
        <template slot-scope="scope">{{ scope.row.updated_at | formatDate }}</template>
      </el-table-column>
      <el-table-column label="操作" width="158">
        <template slot-scope="scope">
          <el-button @click="updateConfigValue(scope.row)" size="small" type="primary" icon="el-icon-edit">编辑
          </el-button>
          <el-popover placement="top" width="160" v-model="scope.row.visible" style="margin-left: 10px">
            <p>确定要删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
              <el-button type="primary" size="mini" @click="deleteConfigValue(scope.row)">确定</el-button>
            </div>
            <el-button type="danger" icon="el-icon-delete" size="mini" slot="reference">删除</el-button>
          </el-popover>
        </template>
      </el-table-column>
    </el-table>
    <el-pagination :current-page="page" :page-size="pageSize" :page-sizes="[10, 30, 50, 100]"
                   :style="{float:'right',padding:'20px'}"
                   :total="total"
                   @current-change="handleCurrentChange"
                   @size-change="handleSizeChange"
                   layout="total, sizes, prev, pager, next, jumper"></el-pagination>

    <el-dialog :before-close="closeDialog" :visible.sync="dialogFormVisible" :title="dialogTitle" width="480px">
      <el-form ref="elForm" :model="formData" :rules="rules" size="medium" label-width="110px">
        <el-form-item label="展示值" prop="label">
          <el-input v-model="formData.label" placeholder="请输入展示值" clearable :style="{width: '100%'}"></el-input>
        </el-form-item>
        <el-form-item label="配置值" prop="value">
          <el-input v-model="formData.value" placeholder="请输入配置值" clearable :style="{width: '100%'}"></el-input>
        </el-form-item>
        <el-form-item label="排序" prop="sort">
          <el-input v-model.number="formData.sort" placeholder="排序" clearable :style="{width: '100%'}"></el-input>
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
  createConfigValue,
  deleteConfigValue,
  updateConfigValue,
  findConfigValue,
  getConfigValueList
} from "@/api/configValue";
import {formatTimeToStr} from "@/utils/data";
import infoList from "@/components/mixins/infoList";

export default {
  name: "ConfigValue",
  mixins: [infoList],
  data() {
    return {
      listApi: getConfigValueList,
      dialogFormVisible: false,
      dialogTitle: "新增配置",
      visible: false,
      type: "",
      formData: {
        label: null,
        value: null,
        status: true,
        sort: null
      },
      rules: {
        label: [
          {
            required: true,
            message: "请输入展示值",
            trigger: "blur"
          }
        ],
        value: [
          {
            required: true,
            message: "请输入配置值",
            trigger: "blur"
          }
        ],
        sort: [
          {
            required: true,
            message: "请输入排序",
            trigger: "blur"
          }
        ]
      }
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
    }
  },
  methods: {
    onSubmit() {
      this.page = 1;
      this.pageSize = 10;
      this.getTableData();
    },
    async updateConfigValue(row) {
      const res = await findConfigValue(row.id);
      this.type = "update";
      if (res.code == 200) {
        this.formData = res.data;
        this.formData.status = this.formData.status == 1;
        this.dialogTitle = "编辑配置";
        this.dialogFormVisible = true;
      }
    },
    closeDialog() {
      this.dialogFormVisible = false;
      this.formData = {
        label: null,
        value: null,
        status: true,
        sort: null,
        config_id: ""
      };
    },
    async deleteConfigValue(row) {
      this.visible = false;
      const res = await deleteConfigValue(row.id);
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "删除成功"
        });
        await this.getTableData();
      }
    },
    async enterDialog() {
      this.formData.config_id = Number(this.$route.params.id)
      this.$refs['elForm'].validate(async valid => {
        if (!valid) return
        let res;
        switch (this.type) {
          case "create":
            res = await createConfigValue(this.formData);
            break;
          case "update":
            res = await updateConfigValue(this.formData.id, this.formData);
            break;
          default:
            res = await createConfigValue(this.formData);
            break;
        }
        if (res.code == 200) {
          this.$message({
            type: "success",
            message: "操作成功"
          })
          this.closeDialog();
          await this.getTableData();
        }
      })

    },
    openDialog() {
      this.type = "create";
      this.dialogTitle = "新增配置";
      this.dialogFormVisible = true;
    }
  },
  created() {
    this.searchInfo.config_id = this.$route.params.id
    this.getTableData();
  }
};
</script>

<style>
</style>