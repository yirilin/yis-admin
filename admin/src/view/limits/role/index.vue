<template>
  <div class="authority">
    <div class="button-box clearflex">
      <el-button @click="addAuthority('0')" type="primary" icon="el-icon-plus">新增角色</el-button>
    </div>
    <el-table :data="tableData"
              :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
              border
              row-key="id"
              stripe
              style="width: 100%">
      <el-table-column label="角色名称" min-width="180" prop="name"></el-table-column>
      <el-table-column label="角色id" min-width="180" prop="id"></el-table-column>
      <el-table-column label="操作时间" prop="updated_at" min-width="180">
        <template slot-scope="scope">{{ scope.row.updated_at | formatDate }}</template>
      </el-table-column>
      <el-table-column fixed="right" label="操作" width="366">
        <template slot-scope="scope">
          <el-button @click="opdendrawer(scope.row)" size="small" type="primary" icon="el-icon-check">设置权限</el-button>
          <el-button @click="addAuthority(scope.row.id)" icon="el-icon-plus" size="small" type="primary">新增子角色
          </el-button>
          <el-button @click="editAuthority(scope.row)" icon="el-icon-edit" size="small" type="primary">编辑</el-button>
          <el-button @click="deleteAuth(scope.row)" icon="el-icon-delete" size="small" type="danger">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 新增角色弹窗 -->
    <el-dialog :title="dialogTitle" :visible.sync="dialogFormVisible" width="480px">
      <el-form :model="form" :rules="rules" ref="authorityForm">
        <el-form-item label="父级角色" label-width="80px" prop="pid">
          <el-cascader
              :disabled="dialogType == 'add'"
              :options="AuthorityOption"
              :props="{
              checkStrictly: true,
              label: 'name',
              value: 'id',
              disabled: 'disabled',
              emitPath: false,
            }"
              :show-all-levels="false"
              filterable
              v-model="form.pid"
          ></el-cascader>
        </el-form-item>
        <el-form-item label="角色名称" label-width="80px" prop="name">
          <el-input autocomplete="off" v-model="form.name"></el-input>
        </el-form-item>
      </el-form>
      <div class="dialog-footer" slot="footer">
        <el-button @click="closeDialog">取消</el-button>
        <el-button @click="enterDialog" type="primary">确定</el-button>
      </div>
    </el-dialog>

    <el-drawer :visible.sync="drawer" :with-header="true" size="40%" title="角色配置" v-if="drawer">
      <el-tabs :before-leave="autoEnter" class="role-box" type="border-card">
        <el-tab-pane label="角色菜单">
          <Menus :row="activeRow" ref="menus"/>
        </el-tab-pane>
      </el-tabs>
    </el-drawer>
  </div>
</template>

<script>

import {
  getAuthorityList,
  deleteAuthority,
  createAuthority,
  updateAuthority,
} from "@/api/authority";

import Menus from "@/view/limits/role/components/menus";
import {formatTimeToStr} from "@/utils/data";

import infoList from "@/components/mixins/infoList";

export default {
  name: "Authority",
  mixins: [infoList],
  data() {
    return {
      AuthorityOption: [
        {
          id: "0",
          name: "根角色",
        },
      ],
      listApi: getAuthorityList,
      drawer: false,
      dialogType: "add",
      activeRow: {},
      activeUserId: 0,
      dialogTitle: "新增角色",
      dialogFormVisible: false,
      apiDialogFlag: false,
      form: {
        id: "",
        name: "",
        pid: "0",
        menu_ids: [],
      },
      rules: {
        name: [
          {required: true, message: "请输入角色名", trigger: "blur"},
        ],
        pid: [
          {required: true, message: "请选择请求方式", trigger: "blur"},
        ],
      },
    };
  },
  components: {
    Menus,
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
  },
  methods: {
    autoEnter(activeName, oldActiveName) {
      const paneArr = ["menus"];
      if (oldActiveName) {
        if (this.$refs[paneArr[oldActiveName]].needConfirm) {
          this.$refs[paneArr[oldActiveName]].enterAndNext();
          this.$refs[paneArr[oldActiveName]].needConfirm = false;
        }
      }
    },
    opdendrawer(row) {
      this.drawer = true;
      this.activeRow = row;
    },
    //删除角色
    deleteAuth(row) {
      this.$confirm("此操作将永久删除该角色, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning",
      })
          .then(async () => {
            const res = await deleteAuthority(row.id);
            if (res.code == 200) {
              this.$message({
                type: "success",
                message: "删除成功!",
              });
              this.getTableData();
            }
          })
          .catch(() => {
            this.$message({
              type: "info",
              message: "已取消删除",
            });
          });
    },
    //初始化表单
    initForm() {
      if (this.$refs.authorityForm) {
        this.$refs.authorityForm.resetFields();
      }
      this.form = {
        id: "",
        name: "",
        pid: "0",
      };
    },
    //关闭窗口
    closeDialog() {
      this.initForm();
      this.dialogFormVisible = false;
      this.apiDialogFlag = false;
    },
    //确定弹窗

    async enterDialog() {
      if (this.form.id == "0") {
        this.$message({
          type: "error",
          message: "角色id不能为0",
        });
        return false;
      }
      this.$refs.authorityForm.validate(async (valid) => {
        if (valid) {
          switch (this.dialogType) {
            case "add": {
              const res = await createAuthority(this.form);
              if (res.code == 200) {
                this.$message({
                  type: "success",
                  message: "添加成功!",
                });
                this.getTableData();
                this.closeDialog();
              }
            }
              break;
            case "edit": {
              const res = await updateAuthority(
                  this.form.id,
                  this.form
              );
              if (res.code == 200) {
                this.$message({
                  type: "success",
                  message: "编辑成功!",
                });
                this.getTableData();
                this.closeDialog();
              }
            }
              break;
          }

          this.initForm();
          this.dialogFormVisible = false;
        }
      });
    },
    setOptions() {
      this.AuthorityOption = [
        {
          id: "0",
          name: "根角色",
        },
      ];
      this.setAuthorityOptions(this.tableData, this.AuthorityOption, false);
    },
    setAuthorityOptions(AuthorityData, optionsData, disabled) {
      this.form.id = String(this.form.id);
      AuthorityData &&
      AuthorityData.map((item) => {
        if (item.children && item.children.length) {
          const option = {
            id: item.id,
            name: item.name,
            disabled: disabled || item.id == this.form.id,
            children: [],
          };
          this.setAuthorityOptions(
              item.children,
              option.children,
              disabled || item.id == this.form.id
          );
          optionsData.push(option);
        } else {
          const option = {
            id: item.id,
            name: item.name,
            disabled: disabled || item.id == this.form.id,
          };
          optionsData.push(option);
        }
      });
    },
    //增加角色
    addAuthority(pid) {
      this.initForm();
      if (pid == '0') {
        this.dialogTitle = "新增角色";
      } else {
        this.dialogTitle = "新增子角色";
      }
      this.dialogType = "add";
      this.form.pid = pid;
      this.setOptions();
      this.dialogFormVisible = true;
    },
    //编辑角色
    editAuthority(row) {
      this.dialogTitle = "编辑角色";
      this.dialogType = "edit";
      for (let key in this.form) {
        this.form[key] = row[key];
      }
      this.setOptions();
      this.dialogFormVisible = true;
    },
  },
  async created() {
    this.pageSize = 999;
    await this.getTableData();
  },
};
</script>
<style lang="scss">
.authority {
  .el-input-number {
    margin-left: 15px;

    span {
      display: none;
    }
  }

  .button-box {
    padding: 10px 20px;

    .el-button {
      float: right;
    }
  }
}

.role-box {
  .el-tabs__content {
    height: calc(100vh - 150px);
    overflow: auto;
  }
}
</style>