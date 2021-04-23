<template>
  <div>
    <div class="search-term">
      <el-form :inline="true" :model="searchInfo" class="demo-form-inline">
        <el-form-item>
          <el-input placeholder="操作人" clearable v-model="searchInfo.username"></el-input>
        </el-form-item>
        <el-form-item>
          <el-input placeholder="请求路径" clearable v-model="searchInfo.path"></el-input>
        </el-form-item>
        <el-form-item>
          <el-input placeholder="状态码" clearable v-model="searchInfo.status"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button @click="onSubmit" type="primary" icon="el-icon-search">查询</el-button>
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
    <el-table :data="tableData" @selection-change="handleSelectionChange" border ref="multipleTable" stripe
              style="width: 100%" tooltip-effect="dark">
      <el-table-column type="selection" width="55"></el-table-column>
      <el-table-column label="操作时间" prop="updated_at" width="158">
        <template slot-scope="scope">{{ scope.row.updated_at | formatDate }}</template>
      </el-table-column>
      <el-table-column label="操作人" prop="username" min-width="120"></el-table-column>
      <el-table-column label="状态码" prop="status" min-width="120">
        <template slot-scope="scope">
          <div>
            <el-tag type="success">{{ scope.row.status }}</el-tag>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="请求ip" prop="ip" min-width="120"></el-table-column>
      <el-table-column label="请求路径" prop="path" min-width="240"></el-table-column>
      <el-table-column label="请求耗时" prop="latency" min-width="120"></el-table-column>
      <el-table-column label="请求" prop="path" min-width="120">
        <template slot-scope="scope">
          <div>
            <el-popover placement="top-start" trigger="hover" v-if="scope.row.body">
              <div class="popover-box">
                <pre>{{ fmtBody(scope.row.body) }}</pre>
              </div>
              <i class="el-icon-view" slot="reference"></i>
            </el-popover>
            <span v-else>无</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="响应消息" prop="path" min-width="120">
        <template slot-scope="scope">
          <div>
            <el-popover placement="top-start" trigger="hover" v-if="scope.row.message">
              <div class="popover-box">
                <pre>{{ fmtBody(scope.row.message) }}</pre>
              </div>
              <i class="el-icon-view" slot="reference"></i>
            </el-popover>
            <span v-else>无</span>
          </div>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="88">
        <template slot-scope="scope">
          <el-popover placement="top" v-model="scope.row.visible" width="160">
            <p>确定要删除吗？</p>
            <div style="text-align: right; margin: 0">
              <el-button @click="scope.row.visible = false" size="mini" type="text">取消</el-button>
              <el-button @click="deleteOperationRecord(scope.row)" size="mini" type="primary">确定</el-button>
            </div>
            <el-button icon="el-icon-delete" size="mini" slot="reference" type="danger">删除</el-button>
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
  </div>
</template>

<script>
import {
  deleteOperationRecord,
  getOperationRecordList,
  deleteOperationRecordByIds
} from '@/api/operationRecord'
import {formatTimeToStr} from '@/utils/data'
import infoList from '@/components/mixins/infoList'

export default {
  name: 'operationRecord',
  mixins: [infoList],
  data() {
    return {
      listApi: getOperationRecordList,
      dialogFormVisible: false,
      visible: false,
      type: '',
      deleteVisible: false,
      multipleSelection: [],
      formData: {
        ip: null,
        method: null,
        path: null,
        status: null,
        latency: null,
        agent: null,
        message: null,
        user_id: null
      }
    }
  },
  filters: {
    formatDate: function (time) {
      if (time != null && time != '') {
        var date = new Date(time)
        return formatTimeToStr(date, 'yyyy-MM-dd hh:mm:ss')
      } else {
        return ''
      }
    },
    formatBoolean: function (bool) {
      if (bool != null) {
        return bool ? '是' : '否'
      } else {
        return ''
      }
    }
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
      const res = await deleteOperationRecordByIds(JSON.stringify(ids))
      if (res.code == 200) {
        this.$message({
          type: 'success',
          message: '删除成功'
        })
        this.deleteVisible = false
        this.page = 1
        this.getTableData()
      }
    },
    async deleteOperationRecord(row) {
      this.visible = false
      const res = await deleteOperationRecord(row.id)
      if (res.code == 200) {
        this.$message({
          type: 'success',
          message: '删除成功'
        })
        this.getTableData()
      }
    },
    fmtBody(value) {
      try {
        return JSON.parse(value)
      } catch (err) {
        return value
      }
    }
  },
  created() {
    this.getTableData()
  }
}
</script>

<style lang="scss">
.table-expand {
  padding-left: 60px;
  font-size: 0;

  label {
    width: 90px;
    color: #99a9bf;

    .el-form-item {
      margin-right: 0;
      margin-bottom: 0;
      width: 50%;
    }
  }
}

.popover-box {
  background: #fff;
  color: #409eff;
  height: 240px;
  width: 180px;
  overflow: auto;
}

.popover-box::-webkit-scrollbar {
  display: none; /* Chrome Safari */
}
</style>