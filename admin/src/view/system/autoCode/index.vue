<template>
  <div>
    <el-collapse v-model="activeNames">
      <el-collapse-item name="1">
        <template slot="title">
          <div :style="{ fontSize: '16px', paddingLeft: '20px' ,color:'#606266'}">
            请选择数据库表
          </div>
        </template>
        <el-form ref="getTableForm" :rules="rules" :inline="true" :model="dbform" label-width="120px">
          <el-form-item label="数据库名" prop="dbName">
            <el-select @change="getTable" v-model="dbform.dbName" filterable placeholder="请选择数据库">
              <el-option v-for="item in dbOptions" :key="item.database" :label="item.database"
                         :value="item.database"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="表名" prop="tableName">
            <el-select v-model="dbform.tableName" :disabled="!dbform.dbName" filterable placeholder="请选择表">
              <el-option v-for="item in tableOptions" :key="item.tableName" :label="item.tableName"
                         :value="item.tableName"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button @click="getColumn" type="primary" icon="el-icon-s-check">确定</el-button>
          </el-form-item>
        </el-form>
      </el-collapse-item>
    </el-collapse>

    <br/>
    <el-form ref="autoCodeForm" :rules="rules" :model="form" label-width="120px" :inline="true">
      <el-form-item label="spacePrefix" prop="spacePrefix">
        <el-select v-model="form.spacePrefix" filterable placeholder="请选择命名空间前缀">
          <el-option v-for="item in spacePrefixOptions" :key="item.value" :label="item.label" :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="nameSpace" prop="nameSpace">
        <el-input v-model="form.nameSpace" placeholder="Business" style="width: 217px"></el-input>
      </el-form-item>
      <el-form-item label="ClassName" prop="className">
        <el-input v-model="form.className" placeholder="首字母自动转换大写" style="width: 217px"></el-input>
      </el-form-item>
    </el-form>

    <br/>
    <el-table :data="form.fields" border stripe>
      <el-table-column type="index" label="序列" width="55"></el-table-column>
      <el-table-column prop="fieldName" min-width="120" label="字段名"></el-table-column>
      <el-table-column prop="dataType" label="字段类型" min-width="120"></el-table-column>
      <el-table-column prop="dataTypeLong" label="字段长度" min-width="120"></el-table-column>
      <el-table-column prop="comment" label="字段描述" min-width="120"></el-table-column>
      <el-table-column prop="fieldSearchType" label="搜索方式" min-width="120">
        <template slot-scope="scope">
          <el-cascader
              v-model="scope.row.fieldSearchType"
              :options="typeSearchOptions"
              :show-all-levels="false"
              :props="{
                checkStrictly: false,
                label: 'label',
                value: 'value',
                disabled: 'disabled',
                emitPath: false,
              }"
              filterable>
          </el-cascader>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="208">
        <template slot-scope="scope">
          <el-button size="mini" type="text" :disabled="scope.$index == 0"
                     @click="moveUpField(scope.$index)">上移
          </el-button>
          <el-button size="mini" type="text" :disabled="scope.$index + 1 == form.fields.length"
                     @click="moveDownField(scope.$index)">下移
          </el-button>
          <el-popover placement="top" v-model="scope.row.visible" style="margin-left: 10px">
            <p>确定删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
              <el-button type="primary" size="mini" @click="deleteField(scope.$index)">确定</el-button>
            </div>
            <el-button size="mini" type="danger" icon="el-icon-delete" slot="reference">删除</el-button>
          </el-popover>
        </template>
      </el-table-column>
    </el-table>
    <!-- 组件列表 -->
    <div class="button-box clearflex">
      <el-button @click="enterForm" type="primary" icon="el-icon-magic-stick">生成代码</el-button>
    </div>
  </div>
</template>
<script>

import {toUpperCase, toHump} from "@/utils/stringFun.js";
import {createTemp, getDB, getTable, getColumn} from "@/api/autoCode.js";

export default {
  name: "autoCode",
  components: {},
  data() {
    return {
      activeNames: ["1"],
      dbform: {
        dbName: "",
        tableName: "",
      },
      dbOptions: [],
      tableOptions: [],
      spacePrefixOptions: [
        {label: 'Admin', value: 'Admin'},
        {label: 'Api', value: 'Api'}
      ],
      addFlag: "",
      fdMap: {},
      form: {
        spacePrefix: "",//命名空间前缀
        nameSpace: "",//命名空间
        className: "",//类名
        tableName: "",//表名
        apiName: "",//API地址名（小驼峰）
        primaryKey: "id",//模型主键
        columns: [],//模型列
        fields: [],//字段用于vue文件生成处理
      },
      rules: {
        spacePrefix: [
          {required: true, message: "请选择命名空间前缀", trigger: "blur"},
        ],
        nameSpace: [
          {required: true, message: "请输入命名空间名称", trigger: "blur"},
        ],
        dbName: [
          {required: true, message: "请选择相应的数据库", trigger: "blur"},
        ],
        className: [
          {required: true, message: "请输入结构体名称", trigger: "blur"},
        ],
        tableName: [
          {required: true, message: "请输入数据库表名称", trigger: "blur"},
        ],
        primaryKey: [
          {required: true, message: "请输入主键字段", trigger: "blur"},
        ],
      },
      typeSearchOptions: [
        {label: "=", value: "="},
        {label: "<>", value: "<>"},
        {label: ">", value: ">"},
        {label: "<", value: "<"},
        {label: "Like", value: "Like"},
        {label: "LikeBefore", value: "LikeBefore"},
        {label: "LikeEnd", value: "LikeEnd"},
        {label: "In", value: "In"},
        {label: "NotIn", value: "NotIn"},
        {label: "Between", value: "Between"},
        {label: "NotBetween", value: "NotBetween"},
        {label: "IsNull", value: "IsNull"},
        {label: "NotNull", value: "NotNull"},
        {label: "Raw", value: "Raw"}
      ],
      dialogMiddle: {},
      bk: {},
      dialogFlag: false,
    };
  },
  methods: {
    moveUpField(index) {
      if (index == 0) {
        return;
      }
      const oldUpField = this.form.fields[index - 1];
      this.form.fields.splice(index - 1, 1);
      this.form.fields.splice(index, 0, oldUpField);
    },
    moveDownField(index) {
      const fCount = this.form.fields.length;
      if (index == fCount - 1) {
        return;
      }
      const oldDownField = this.form.fields[index + 1];
      this.form.fields.splice(index + 1, 1);
      this.form.fields.splice(index, 0, oldDownField);
    },
    deleteField(index) {
      this.form.fields.splice(index, 1);
    },
    async enterForm() {
      if (!(this.dbform.dbName || this.dbform.tableName)) {
        this.$message({
          type: "error",
          message: "请选择数据库及相应的数据表",
        });
        return false;
      }
      if (this.form.fields.length <= 0) {
        this.$message({
          type: "error",
          message: "请填写至少一个field",
        });
        return false;
      }
      if (this.form.fields.some((item) => item.fieldName == this.form.className)) {
        this.$message({
          type: "error",
          message: "存在与类同名的字段",
        });
        return false;
      }
      var tmpArr1 = ["id"];
      var tmpArr2 = ["status", "created_at", "updated_at", "is_delete"];
      this.form.columns = JSON.stringify(tmpArr1.concat(this.form.columns));
      if (this.form.columns.length > 0) {
        this.form.fields.forEach(function (item) {
          tmpArr1.push(item.columnName);
        });
      }
      tmpArr2.forEach(function (item) {
        tmpArr1.push(item);
      });
      this.form.columns = JSON.stringify(tmpArr1);
      this.$refs.autoCodeForm.validate(async (valid) => {
        if (valid) {
          this.form.className = toUpperCase(this.form.className);
          const data = await createTemp(this.form).catch(res => console.log(res));
          if (data && data.code == 200) {
            await this.$alert(data.data, '操作成功-请添加路由', {
              confirmButtonText: '确定'
            });
          }
        } else {
          return false;
        }
      });
    },
    async getDb() {
      const res = await getDB();
      if (res.code == 200) {
        this.dbOptions = res.data.dbs;
      }
    },
    async getTable() {
      const res = await getTable({dbName: this.dbform.dbName});
      if (res.code == 200) {
        this.tableOptions = res.data.tables;
      }
      this.dbform.tableName = "";
    },
    async getColumn() {
      const gormModelList = ["id", "status", "created_at", "updated_at", "is_delete"];
      const res = await getColumn(this.dbform);
      if (res.code == 200) {
        this.dbform.tableName = this.dbform.tableName.replace("ys_", "");
        const tbHump = toHump(this.dbform.tableName);
        this.form.spacePrefix = 'Admin';
        this.form.nameSpace = 'Business';
        this.form.className = toUpperCase(tbHump);
        this.form.tableName = this.dbform.tableName;
        this.form.apiName = tbHump;
        this.form.fields = [];
        res.data.columns &&
        res.data.columns.map((item) => {
          if (!gormModelList.some((gormfd) => gormfd == item.columnName)) {
            const fbHump = item.columnName;
            this.form.columns.push(item.columnName);
            this.form.fields.push({
              fieldName: item.columnName,
              fieldDesc: item.columnComment || fbHump + "字段",
              fieldType: this.fdMap[item.dataType],
              dataType: item.dataType,
              fieldJson: fbHump,
              dataTypeLong: item.dataTypeLong,
              columnName: item.columnName,
              comment: item.columnComment,
              fieldSearchType: "",
              dictType: "",
            });
          }
        });
      }
    },
  },
  created() {
    this.getDb();
  },
};
</script>

<style scope lang="scss">
.button-box {
  padding: 10px 20px;

  .el-button {
    float: right;
  }
}
</style>
