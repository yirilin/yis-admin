<template>
  <div>
    <!-- 查询表单开始 -->
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        {{search}}
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
    <el-table :data="tableData" @selection-change="handleSelectionChange" border ref="multipleTable" stripe style="width: 100%" tooltip-effect="dark">
      <el-table-column type="selection" width="55"></el-table-column>
      {{table}}
      <el-table-column label="操作时间" prop="updated_at" width="158">
          <template slot-scope="scope">{{scope.row.updated_at | formatDate}}</template>
      </el-table-column>
      <el-table-column label="操作" width="158">
        <template slot-scope="scope">
          <el-button icon="el-icon-edit" @click="update{{className}}(scope.row)" size="small" type="primary">编辑</el-button>
          <el-popover placement="top" width="160" v-model="scope.row.visible" style="margin-left: 10px">
            <p>确定要删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
              <el-button type="primary" size="mini" @click="delete{{className}}(scope.row)">确定</el-button>
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
                   layout="total, sizes, prev, pager, next, jumper">
    </el-pagination>
    <!-- 列表展示结束 -->

    <!-- 增改表单开始 -->
      <el-dialog :before-close="closeDialog" :title="dialogTitle" :visible.sync="dialogFormVisible" width="480px">
        <el-form ref="elForm" :model="formData" :rules="rules" size="mini" label-width="100px" label-position="left">
        {{form}}
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
    create{{className}},
    update{{className}},
    find{{className}},
    get{{className}}List,
    delete{{className}}
} from "@/api/{{apiName}}";
import { formatTimeToStr } from "@/utils/data";
import infoList from "@/components/mixins/infoList";
export default {
  name: "{{className}}",
  mixins: [infoList],
  data() {
    return {
      listApi: get{{className}}List,
      dialogFormVisible: false,
      dialogTitle: "新增",
      visible: false,
      type: "",
      deleteVisible: false,
      multipleSelection: [],
      formData: {{formData}},
      rules: {{rules}},
    };
  },
  filters: {
    formatDate: function(time) {
      if (time != null && time != "") {
        var date = new Date(time);
        return formatTimeToStr(date, "yyyy-MM-dd hh:mm:ss");
      } else {
        return "";
      }
    },
    formatBoolean: function(bool) {
      if (bool != null) {
        return bool ? "是" :"否";
      } else {
        return "";
      }
    },
  },
  methods: {
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
        const res = await delete{{className}}(JSON.stringify(ids))
        if (res.code == 200) {
          this.$message({
            type: 'success',
            message: '批量删除成功'
          })
          this.deleteVisible = false;
          await this.getTableData()
        }
      },
    async update{{className}}(row) {
      const res = await find{{className}}(row.id);
      this.type = "update";
      if (res.code == 200) {
        this.dialogTitle = "编辑";
        this.formData = res.data;
        this.dialogFormVisible = true;
      }
    },
    closeDialog() {
      this.dialogFormVisible = false;
      this.formData = {{formData}};
    },
    async delete{{className}}(row) {
      this.visible = false;
      const res = await delete{{className}}(row.id);
      if (res.code == 200) {
        this.$message({
          type: "success",
          message: "删除成功"
        });
        await this.getTableData();
      }
    },
    async enterDialog() {
      let res;
      switch (this.type) {
        case "create":
          res = await create{{className}}(this.formData);
          break;
        case "update":
          res = await update{{className}}(this.formData.id, this.formData);
          break;
        default:
          res = await create{{className}}(this.formData);
          break;
      }
      if (res.code == 200) {
        this.$message({
          type:"success",
          message:"操作成功"
        })
        this.closeDialog();
        await this.getTableData();
      }
    },
    openDialog() {
      this.dialogTitle = "新增";
      this.type = "create";
      for (let key in this.formData) {
        this.formData[key] = '';
      }
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
