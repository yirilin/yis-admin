<template>
  <div>
    <div class="button-box clearflex">
      <el-button @click="addMenu('0')" type="primary" icon="el-icon-plus">新增根菜单</el-button>
    </div>

    <el-table :data="tableData" border row-key="id" stripe>
      <el-table-column label="展示名称" min-width="120" prop="authorityName">
        <template slot-scope="scope">
          <span>{{ scope.row.meta.title }}</span>
        </template>
      </el-table-column>
      <el-table-column label="路由Name" min-width="120" prop="name"></el-table-column>
      <el-table-column label="路由Path" min-width="120" prop="path"></el-table-column>
      <el-table-column label="是否隐藏" min-width="120" prop="hidden">
        <template slot-scope="scope">
          <span>{{ scope.row.hidden ? "隐藏" : "显示" }}</span>
        </template>
      </el-table-column>
      <el-table-column label="排序" min-width="120" prop="sort"></el-table-column>
      <el-table-column label="图标" min-width="120" prop="authorityName">
        <template slot-scope="scope">
          <i :class="`el-icon-${scope.row.meta.icon}`"></i>
          <span>{{ scope.row.meta.icon }}</span>
        </template>
      </el-table-column>
      <el-table-column label="文件路径" min-width="240" prop="component"></el-table-column>
      <el-table-column label="操作时间" prop="updated_at" width="158">
        <template slot-scope="scope">{{ scope.row.updated_at | formatDate }}</template>
      </el-table-column>
      <el-table-column fixed="right" label="操作" width="266">
        <template slot-scope="scope">
          <el-button @click="addMenu(scope.row.id)" size="small" type="primary" icon="el-icon-plus">添加子菜单</el-button>
          <el-button @click="editMenu(scope.row.id)" size="small" type="primary" icon="el-icon-edit">编辑</el-button>
          <el-button @click="deleteMenu(scope.row.id)" size="small" type="danger" icon="el-icon-delete">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :before-close="handleClose" :title="dialogTitle" :visible.sync="dialogFormVisible" width="800px">
      <el-form :inline="true" :model="form" :rules="rules" label-position="top" label-width="85px" ref="menuForm">
        <el-form-item label="路由name" prop="path" style="width:30%">
          <el-input @change="changeName" autocomplete="off" placeholder="唯一英文字符串" v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item prop="path" style="width:30%">
          <div style="display:inline-block" slot="label">路由path
            <el-checkbox style="float:right;margin-left:20px;" v-model="checkFlag">添加参数</el-checkbox>
          </div>
          <el-input :disabled="!checkFlag" autocomplete="off" placeholder="在后方拼接参数" v-model="form.path"></el-input>
        </el-form-item>
        <el-form-item label="是否隐藏" style="width:30%">
          <el-select placeholder="是否在列表隐藏" v-model="form.hidden">
            <el-option :value="false" label="否"></el-option>
            <el-option :value="true" label="是"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="父节点" style="width:30%">
          <el-cascader :disabled="!this.isEdit" :options="menuOption"
                       :props="{ checkStrictly: true,label:'title',value:'id',disabled:'disabled',emitPath:false}"
                       :show-all-levels="false" filterable v-model="form.pid"></el-cascader>
        </el-form-item>
        <el-form-item label="文件路径" prop="component" style="width:30%">
          <el-input autocomplete="off" v-model="form.component"></el-input>
        </el-form-item>
        <el-form-item label="展示名称" prop="meta.title" style="width:30%">
          <el-input autocomplete="off" v-model="form.meta.title"></el-input>
        </el-form-item>
        <el-form-item label="图标" prop="meta.icon" style="width:30%">
          <icon :meta="form.meta">
            <template slot="prepend">el-icon-</template>
          </icon>
        </el-form-item>
        <el-form-item label="排序标记" prop="sort" style="width:30%">
          <el-input autocomplete="off" v-model.number="form.sort"></el-input>
        </el-form-item>
        <el-form-item label="keepAlive" prop="meta.keepAlive" style="width:30%">
          <el-select placeholder="是否keepAlive缓存页面" v-model="form.meta.keepAlive">
            <el-option :value="false" label="否"></el-option>
            <el-option :value="true" label="是"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div class="warning">新增菜单需要在角色管理内配置权限才可使用</div>
      <div>
        <el-button size="small" type="primary" icon="el-icon-edit" @click="addParameter(form)">新增参数</el-button>
        <el-table :data="form.parameters" stripe style="width: 100%">
          <el-table-column prop="type" label="参数类型" width="180">
            <template slot-scope="scope">
              <el-select v-model="scope.row.type" placeholder="请选择">
                <el-option key="query" value="query" label="query"></el-option>
                <el-option key="params" value="params" label="params"></el-option>
              </el-select>
            </template>
          </el-table-column>
          <el-table-column prop="key" label="参数key" width="180">
            <template slot-scope="scope">
              <div>
                <el-input v-model="scope.row.key"></el-input>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="value" label="参数值">
            <template slot-scope="scope">
              <div>
                <el-input v-model="scope.row.value"></el-input>
              </div>
            </template>
          </el-table-column>
          <el-table-column>
            <template slot-scope="scope">
              <div>
                <el-button type="danger" size="small" icon="el-icon-delete"
                           @click="deleteParameter(form.parameters,scope.$index)">删除
                </el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="dialog-footer" slot="footer">
        <el-button @click="closeDialog">取 消</el-button>
        <el-button @click="enterDialog" type="primary">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  updateBaseMenu,
  getMenuList,
  addBaseMenu,
  deleteBaseMenu,
  getBaseMenuById
} from "@/api/menu";
import infoList from "@/components/mixins/infoList";
import icon from "@/view/limits/menu/icon";
import {formatTimeToStr} from "@/utils/data";

export default {
  name: "Menus",
  mixins: [infoList],
  data() {
    return {
      checkFlag: false,
      listApi: getMenuList,
      dialogFormVisible: false,
      dialogTitle: "新增菜单",
      menuOption: [
        {
          id: "0",
          title: "根菜单"
        }
      ],
      form: {
        id: 0,
        path: "",
        name: "",
        hidden: false,
        pid: "",
        component: "",
        meta: {
          title: "",
          icon: "",
          defaultMenu: false,
          keepAlive: false
        },
        parameters: []
      },
      rules: {
        path: [{required: true, message: "请输入菜单name", trigger: "blur"}],
        component: [
          {required: true, message: "请输入文件路径", trigger: "blur"}
        ],
        "meta.title": [
          {required: true, message: "请输入菜单展示名称", trigger: "blur"}
        ]
      },
      isEdit: false,
      test: ""
    };
  },
  components: {
    icon
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
    addParameter(form) {
      if (!form.parameters) {
        this.$set(form, "parameters", [])
      }
      form.parameters.push({
        type: "query",
        key: "",
        value: ""
      });
    },
    deleteParameter(parameters, index) {
      parameters.splice(index, 1);
    },
    changeName() {
      this.form.path = this.form.name;
    },
    setOptions() {
      this.menuOption = [
        {
          id: "0",
          title: "根目录"
        }
      ];
      this.setMenuOptions(this.tableData, this.menuOption, false);
    },
    setMenuOptions(menuData, optionsData, disabled) {
      menuData &&
      menuData.map(item => {
        if (item.children && item.children.length) {
          const option = {
            title: item.meta.title,
            id: String(item.id),
            disabled: disabled || item.id == this.form.id,
            children: []
          };
          this.setMenuOptions(
              item.children,
              option.children,
              disabled || item.id == this.form.id
          );
          optionsData.push(option);
        } else {
          const option = {
            title: item.meta.title,
            id: String(item.id),
            disabled: disabled || item.id == this.form.id
          };
          optionsData.push(option);
        }
      });
    },
    handleClose(done) {
      this.initForm();
      done();
    },
    //懒加载子菜单
    load(tree, treeNode, resolve) {
      resolve([]);
    },
    //删除菜单
    deleteMenu(id) {
      this.$confirm("此操作将永久删除所有角色下该菜单, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
          .then(async () => {
            const res = await deleteBaseMenu(id);
            if (res.code == 200) {
              this.$message({
                type: "success",
                message: "删除成功!"
              });
              this.getTableData();
            }
          })
          .catch(() => {
            this.$message({
              type: "info",
              message: "已取消删除"
            });
          });
    },
    //初始化弹窗内表格方法
    initForm() {
      this.checkFlag = false;
      this.$refs.menuForm.resetFields();
      this.form = {
        id: 0,
        path: "",
        name: "",
        hidden: false,
        pid: "",
        component: "",
        meta: {
          title: "",
          icon: "",
          defaultMenu: false,
          keepAlive: ""
        }
      };
    },
    //关闭弹窗
    closeDialog() {
      this.initForm();
      this.dialogFormVisible = false;
    },
    //添加menu
    async enterDialog() {
      this.$refs.menuForm.validate(async valid => {
        if (valid) {
          let res;
          if (this.isEdit) {
            res = await updateBaseMenu(this.form.id, this.form);
          } else {
            res = await addBaseMenu(this.form);
          }
          if (res.code == 200) {
            this.$message({
              type: "success",
              message: this.isEdit ? "编辑成功" : "添加成功!"
            });
            this.getTableData();
          }
          this.initForm();
          this.dialogFormVisible = false;
        }
      });
    },
    //添加菜单方法，id为 0则为添加根菜单
    addMenu(id) {
      this.dialogTitle = "新增菜单";
      this.form.pid = String(id);
      this.isEdit = false;
      this.setOptions();
      this.dialogFormVisible = true;
    },
    //修改菜单方法
    async editMenu(id) {
      this.dialogTitle = "编辑菜单";
      const res = await getBaseMenuById(id);
      this.form = res.data;
      this.isEdit = true;
      this.setOptions();
      this.dialogFormVisible = true;
    }
  },
  async created() {
    this.pageSize = 999;
    await this.getTableData();
  }
};
</script>
<style scoped lang="scss">
.button-box {
  padding: 10px 20px;

  .el-button {
    float: right;
  }
}

.warning {
  color: #dc143c;
}
</style>
