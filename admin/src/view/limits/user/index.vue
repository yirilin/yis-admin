<template>
  <div>
    <div class="button-box clearflex">
      <el-button @click="addUser" type="primary" icon="el-icon-plus">新增用户</el-button>
    </div>

    <el-table :data="tableData" border stripe>
      <el-table-column label="头像" min-width="55">
        <template slot-scope="scope">
          <div :style="{ textAlign: 'center' }">
            <CustomPic :picSrc="scope.row.header"/>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="用户名" min-width="120" prop="username"></el-table-column>
      <el-table-column label="姓名" min-width="120" prop="nickname"></el-table-column>
      <el-table-column label="角色" min-width="120">
        <template slot-scope="scope">
          <el-cascader
              @change="changeAuthority(scope.row)"
              v-model="scope.row.authority.id"
              :options="authOptions"
              :show-all-levels="false"
              :props="{
                checkStrictly: true,
                label: 'name',
                value: 'id',
                disabled: 'disabled',
                emitPath: false,
              }"
              filterable>
          </el-cascader>
        </template>
      </el-table-column>
      <el-table-column label="操作时间" prop="updated_at" width="158">
        <template slot-scope="scope">{{ scope.row.updated_at | formatDate }}</template>
      </el-table-column>
      <el-table-column label="操作" width="158">
        <template slot-scope="scope">
          <el-button @click="editUser(scope.row)" icon="el-icon-edit" size="small" type="primary">编辑</el-button>
          <el-popover placement="top" width="160" v-model="scope.row.visible" style="margin-left: 10px">
            <p>确定要删除此用户吗</p>
            <div style="text-align: right; margin: 0">
              <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
              <el-button type="primary" size="mini" @click="deleteUser(scope.row)">确定</el-button>
            </div>
            <el-button type="danger" icon="el-icon-delete" size="small" slot="reference">删除</el-button>
          </el-popover>
        </template>
      </el-table-column>
    </el-table>
    <el-pagination :current-page="page" :page-size="pageSize" :page-sizes="[10, 30, 50, 100]"
                   :style="{ float: 'right', padding: '20px' }"
                   :total="total"
                   @current-change="handleCurrentChange"
                   @size-change="handleSizeChange"
                   layout="total, sizes, prev, pager, next, jumper">
    </el-pagination>

    <el-dialog :visible.sync="addUserDialog" :title="dialogTitle" custom-class="user-dialog" width="480px">
      <el-form :rules="rules" ref="userForm" :model="userInfo">
        <el-form-item label="用户名" label-width="80px" prop="username">
          <el-input v-model="userInfo.username" placeholder="请输入5位以上登陆用户名" style="width: 100%"></el-input>
        </el-form-item>
        <el-form-item label="密码" label-width="80px" prop="password">
          <el-input show-password v-model="userInfo.password" placeholder="请输入6-18位登陆密码" style="width: 100%"></el-input>
        </el-form-item>
        <el-form-item label="姓名" label-width="80px" prop="nickname">
          <el-input v-model="userInfo.nickname" placeholder="姓名" style="width: 100%"></el-input>
        </el-form-item>
        <el-form-item label="角色" label-width="80px" prop="authority_id">
          <el-cascader
              v-model="userInfo.authority_id"
              placeholder="请选择用户角色"
              :options="authOptions"
              :show-all-levels="false"
              :props="{
                checkStrictly: true,
                label: 'name',
                value: 'id',
                disabled: 'disabled',
                emitPath: false,
              }"
              filterable>
          </el-cascader>
        </el-form-item>
        <el-form-item label="头像" label-width="80px">
          <el-upload class="avatar-uploader" :headers="{Authorization:'Bearer '+ token}"
                     action="/admin/file/upload"
                     :show-file-list="false"
                     :on-success="handleAvatarSuccess"
                     :before-upload="beforeAvatarUpload">
            <input name="_method" type="hidden" value="POST">
            <img class="header-img-box" v-if="userInfo.header" :src="userInfo.header"/>
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
          </el-upload>
        </el-form-item>
      </el-form>
      <div class="dialog-footer" slot="footer">
        <el-button @click="closeAddUserDialog">取消</el-button>
        <el-button @click="enterAddUserDialog" type="primary">确定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
const path = process.env.VUE_APP_BASE_API;
import {
  getUserList,
  setUserAuthority,
  register,
  updateUser,
  deleteUser,
} from "@/api/user";
import {getAuthorityList} from "@/api/authority";
import infoList from "@/components/mixins/infoList";
import {mapGetters} from "vuex";
import CustomPic from "@/components/customPic";
import {formatTimeToStr} from "@/utils/data";

export default {
  name: "Api",
  mixins: [infoList],
  components: {CustomPic},
  data() {
    return {
      listApi: getUserList,
      path: path,
      authOptions: [],
      addUserDialog: false,
      dialogTitle: "新增用户",
      userInfo: {
        id: "",
        username: "",
        password: "",
        nickname: "",
        header: "",
        authority_id: "",
      },
      rules: {
        username: [
          {required: true, message: "请输入用户名", trigger: "blur"},
          {min: 5, message: "最低5位以上字符", trigger: "blur"},
        ],
        password: [
          {required: true, message: "请输入用户密码", trigger: "blur"},
          {min: 6, max: 18, message: "请输入6-18位密码", trigger: "blur"},
        ],
        nickname: [
          {required: true, message: "请输入用户姓名", trigger: "blur"},
        ],
        authority_id: [
          {required: true, message: "请选择用户角色", trigger: "blur"},
        ],
      },
    };
  },
  computed: {
    ...mapGetters("user", ["token"]),
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
    setOptions(authData) {
      this.authOptions = [];
      this.setAuthorityOptions(authData, this.authOptions);
    },
    setAuthorityOptions(AuthorityData, optionsData) {
      AuthorityData &&
      AuthorityData.map((item) => {
        if (item.children && item.children.length) {
          const option = {
            id: item.id,
            name: item.name,
            children: [],
          };
          this.setAuthorityOptions(item.children, option.children);
          optionsData.push(option);
        } else {
          const option = {
            id: item.id,
            name: item.name,
          };
          optionsData.push(option);
        }
      });
    },
    async deleteUser(row) {
      const res = await deleteUser(row.id);
      if (res.code == 200) {
        await this.getTableData();
        row.visible = false;
      }
    },
    async enterAddUserDialog() {
      this.$refs.userForm.validate(async (valid) => {
        if (valid) {
          let res, msg = '创建成功';
          switch (this.dialogTitle) {
            case "新增用户":
              res = await register(this.userInfo);
              msg = '创建成功';
              break;
            case "编辑用户":
              res = await updateUser(this.userInfo.id, this.userInfo);
              msg = '编辑成功';
              break;
            default:
              res = await register(this.userInfo);
              msg = '创建成功';
              break;
          }
          if (res.code == 200) {
            this.$message({type: "success", message: msg});
          }
          await this.getTableData();
          this.closeAddUserDialog();
        }
      });
    },
    closeAddUserDialog() {
      this.$refs.userForm.resetFields();
      this.addUserDialog = false;
    },
    addUser() {
      this.rules.password[0]['required'] = true;
      this.dialogTitle = "新增用户";
      for (let key in this.userInfo) {
        this.userInfo[key] = '';
      }
      this.addUserDialog = true;
    },
    //编辑
    editUser(row) {
      this.rules.password[0]['required'] = false;
      this.dialogTitle = "编辑用户";
      for (let key in this.userInfo) {
        this.userInfo[key] = row[key];
      }
      this.setAuthorityOptions();
      this.addUserDialog = true;
    },
    async changeAuthority(row) {
      const res = await setUserAuthority(row.uuid, {
        authority_id: row.authority.id,
      });
      if (res.code == 200) {
        row.authority_id = row.authority.id;
        this.$message({type: "success", message: "角色设置成功"});
      }
    },
    handleAvatarSuccess(res) {
      if (res['success']) {
        this.userInfo.header = res.data;
      } else {
        this.$message({type: "error", message: res.msg});
      }
    },
    beforeAvatarUpload(file) {
      const isJPG = file.type === 'image/jpeg';
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!isJPG) {
        this.$message.error('上传头像图片只能是 JPG 格式!');
      }
      if (!isLt2M) {
        this.$message.error('上传头像图片大小不能超过 2MB!');
      }
      return isJPG && isLt2M;
    }
  },
  async created() {
    await this.getTableData();
    const res = await getAuthorityList({page: 1, pageSize: 999});
    this.setOptions(res.data.list);
  },
};
</script>
<style lang="scss">
.button-box {
  padding: 10px 20px;

  .el-button {
    float: right;
  }
}

.user-dialog {
  .header-img-box {
    width: 200px;
    height: 200px;
    border: 1px dashed #ccc;
    border-radius: 20px;
    text-align: center;
    line-height: 200px;
    cursor: pointer;
  }

  .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }

  .avatar-uploader-icon {
    border: 1px dashed #d9d9d9 !important;
    border-radius: 6px;
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }

  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
}

.el-cascader .el-input {
  width: 100%;
}
</style>