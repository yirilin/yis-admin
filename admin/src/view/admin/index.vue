<template>
  <el-container class="layout-cont">
    <el-container :class="[isSider?'openside':'hideside',isMobile ? 'mobile': '']">
      <el-row :class="[isShadowBg?'shadowBg':'']" @click.native="changeShadow()"></el-row>
      <el-aside class="main-cont main-left">
        <div class="tilte">
          <img alt class="logoimg" src="~@/assets/logo.png"/>
          <h2 class="tit-text" v-if="isSider">YisAdmin</h2>
        </div>
        <Aside class="aside"/>
      </el-aside>
      <!-- 分块滑动功能 -->
      <el-main class="main-cont main-right">
        <transition :duration="{ enter: 800, leave: 100 }" mode="out-in" name="el-fade-in-linear">
          <div :style="{width: `calc(100% - ${isMobile?'0px':isCollapse?'54px':'220px'})`}" class="topfix">
            <el-header class="header-cont">
              <div @click="totalCollapse" class="menu-total">
                <i class="el-icon-s-unfold" v-if="isCollapse"></i>
                <i class="el-icon-s-fold" v-else></i>
              </div>
              <el-breadcrumb class="breadcrumb" separator-class="el-icon-arrow-right">
                <el-breadcrumb-item :key="item.path" v-for="item in matched.slice(1,matched.length)">
                  {{ item.meta.title }}
                </el-breadcrumb-item>
              </el-breadcrumb>
              <div class="fl-right right-box">
                <el-dropdown>
                  <span class="header-avatar">
                    <CustomPic :picSrc="userInfo.header"/>
                    <span style="margin-left: 5px">{{ userInfo.nickname }}</span>
                    <i class="el-icon-arrow-down"></i>
                  </span>
                  <el-dropdown-menu class="dropdown-group" slot="dropdown">
                    <el-dropdown-item @click.native="showPassword=true" icon="el-icon-user">修改密码</el-dropdown-item>
                    <el-dropdown-item @click.native="LoginOut" icon="el-icon-table-lamp">退出</el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>
              </div>
            </el-header>
            <HistoryComponent/>
          </div>
        </transition>
        <transition mode="out-in" name="el-fade-in-linear">
          <keep-alive>
            <router-view class="admin-box" v-if="$route.meta.keepAlive"></router-view>
          </keep-alive>
        </transition>
        <transition mode="out-in" name="el-fade-in-linear">
          <router-view class="admin-box" v-if="!$route.meta.keepAlive"></router-view>
        </transition>
        <BottomInfo/>
      </el-main>
    </el-container>
    <el-dialog :visible.sync="showPassword" @close="clearPassword" title="修改密码" width="480px">
      <el-form :model="pwdModify" :rules="rules" label-width="80px" ref="modifyPwdForm">
        <el-form-item :minlength="6" label="原密码" prop="password">
          <el-input show-password v-model="pwdModify.password"></el-input>
        </el-form-item>
        <el-form-item :minlength="6" label="新密码" prop="newPassword">
          <el-input show-password v-model="pwdModify.newPassword"></el-input>
        </el-form-item>
        <el-form-item :minlength="6" label="确认密码" prop="confirmPassword">
          <el-input show-password v-model="pwdModify.confirmPassword"></el-input>
        </el-form-item>
      </el-form>
      <div class="dialog-footer" slot="footer">
        <el-button @click="showPassword=false">取消</el-button>
        <el-button @click="savePassword" type="primary">确定</el-button>
      </div>
    </el-dialog>
  </el-container>
</template>

<script>
import Aside from '@/view/admin/aside'
import HistoryComponent from '@/view/admin/aside/historyComponent/history'
import BottomInfo from '@/view/admin/bottomInfo/bottomInfo'
import {mapGetters, mapActions} from 'vuex'
import {changePassword} from '@/api/user'
import CustomPic from '@/components/customPic'

export default {
  name: 'admin',
  data() {
    return {
      show: false,
      isCollapse: false,
      isSider: true,
      isMobile: false,
      isShadowBg: false,
      showPassword: false,
      pwdModify: {},
      rules: {
        password: [
          {required: true, message: '请输入密码', trigger: 'blur'},
          {min: 6, message: '最少6个字符', trigger: 'blur'}
        ],
        newPassword: [
          {required: true, message: '请输入新密码', trigger: 'blur'},
          {min: 6, message: '最少6个字符', trigger: 'blur'}
        ],
        confirmPassword: [
          {required: true, message: '请输入确认密码', trigger: 'blur'},
          {min: 6, message: '最少6个字符', trigger: 'blur'},
          {
            validator: (rule, value, callback) => {
              if (value !== this.pwdModify.newPassword) {
                callback(new Error('两次密码不一致'))
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ]
      },
      value: ''
    }
  },
  components: {
    Aside,
    HistoryComponent,
    BottomInfo,
    CustomPic
  },
  methods: {
    ...mapActions('user', ['LoginOut']),
    totalCollapse() {
      this.isCollapse = !this.isCollapse
      this.isSider = !this.isCollapse
      this.isShadowBg = !this.isCollapse
      this.$bus.emit('collapse', this.isCollapse)
    },
    changeShadow() {
      this.isShadowBg = !this.isShadowBg
      this.isSider = !!this.isCollapse
      this.totalCollapse()
    },
    savePassword() {
      this.$refs.modifyPwdForm.validate(valid => {
        if (valid) {
          changePassword({
            id: this.userInfo.id,
            username: this.userInfo.username,
            password: this.pwdModify.password,
            newPassword: this.pwdModify.newPassword
          }).then(() => {
            this.$message.success('修改密码成功！')
            this.showPassword = false
          })
        } else {
          return false
        }
      })
    },
    clearPassword() {
      this.pwdModify = {
        password: '',
        newPassword: '',
        confirmPassword: ''
      }
      this.$refs.modifyPwdForm.clearValidate()
    }
  },
  computed: {
    ...mapGetters('user', ['userInfo']),
    title() {
      return this.$route.meta.title || '当前页面'
    },
    matched() {
      return this.$route.matched
    }
  },
  mounted() {
    let screenWidth = document.body.clientWidth
    if (screenWidth < 1000) {
      this.isMobile = true
      this.isSider = false
      this.isCollapse = true
    } else if (screenWidth >= 1000 && screenWidth < 1200) {
      this.isMobile = false
      this.isSider = false
      this.isCollapse = true
    } else {
      this.isMobile = false
      this.isSider = true
      this.isCollapse = false
    }
    this.$bus.emit('collapse', this.isCollapse)
    this.$bus.emit('mobile', this.isMobile)
    window.onresize = () => {
      return (() => {
        let screenWidth = document.body.clientWidth
        if (screenWidth < 1000) {
          this.isMobile = true
          this.isSider = false
          this.isCollapse = true
        } else if (screenWidth >= 1000 && screenWidth < 1200) {
          this.isMobile = false
          this.isSider = false
          this.isCollapse = true
        } else {
          this.isMobile = false
          this.isSider = true
          this.isCollapse = false
        }
        this.$bus.emit('collapse', this.isCollapse)
        this.$bus.emit('mobile', this.isMobile)
      })()
    }
  }
}
</script>

<style lang="scss">
$headerHigh: 52px;
$mainHight: 100vh;
.dropdown-group {
  min-width: 100px;
}

.topfix {
  position: fixed;
  top: 0;
  box-sizing: border-box;
  z-index: 999;
}

.admin-box {
  min-height: calc(100vh - 240px);
  background-color: rgb(255, 255, 255);
  margin-top: 100px;
}

.el-scrollbar__wrap {
  padding-bottom: 17px;
}

.layout-cont {
  .right-box {
    text-align: center;
    vertical-align: middle;

    img {
      vertical-align: middle;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
  }

  .header-cont {
    height: $headerHigh !important;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);
    line-height: $headerHigh;
  }

  .main-cont {
    .breadcrumb {
      line-height: 48px;
      display: inline-block;
      padding: 0 24px;
    }

    &.el-main {
      overflow: auto;
      background: #fff;
    }

    height: $mainHight !important;
    overflow: visible;
    position: relative;

    .menu-total {
      margin-left: -10px;
      float: left;
      margin-top: 10px;
      width: 30px;
      height: 30px;
      line-height: 30px;
      font-size: 30px;
    }

    .aside {
      overflow: auto;

      &::-webkit-scrollbar {
        display: none;
      }
    }

    .el-menu-vertical {
      height: calc(100vh - 64px) !important;

      &:not(.el-menu--collapse) {
        width: 220px;
      }
    }

    .el-menu--collapse {
      width: 54px;

      li {
        .el-tooltip,
        .el-submenu__title {
          padding: 0px 15px !important;
        }
      }
    }

    &::-webkit-scrollbar {
      display: none;
    }

    &.main-left {
      width: auto !important;
    }

    &.main-right {
      .admin-title {
        float: left;
        font-size: 16px;
        vertical-align: middle;
        margin-left: 20px;

        img {
          vertical-align: middle;
        }

        &.collapse {
          width: 53px;
        }
      }
    }
  }
}

.tilte {
  background: #ffffff;
  min-height: 90px;
  line-height: 90px;
  text-align: center;

  .logoimg {
    width: 40px;
    height: 40px;
    vertical-align: middle;
    background: #fff;
    border-radius: 50%;
    padding: 3px;
  }

  .tit-text {
    display: inline-block;
    color: #409EFF;
    font-weight: 480;
    font-size: 20px;
    vertical-align: middle;
  }
}

.header-avatar {
  display: flex;
  justify-content: center;
  align-items: center;
}

.el-input__inner {
  height: 30px;
  line-height: 30px;
}

.el-input__icon {
  line-height: 30px;
}


</style>
